<?php

namespace App\Http\Controllers;

use App\Models\SharedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileBoxController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->integer('folder') ?: null;

        $currentFolder = $folderId
            ? SharedFile::where('type', 'folder')->findOrFail($folderId)
            : null;

        // 現在のフォルダ直下のみ表示（ルートは parent_id = null）
        $files = SharedFile::with('user')
            ->where('parent_id', $folderId)
            ->orderByRaw("CASE WHEN type='folder' THEN 0 ELSE 1 END")
            ->orderBy('file_name')
            ->get();

        // パンくず
        $breadcrumbs = [];
        $node = $currentFolder;
        while ($node) {
            array_unshift($breadcrumbs, $node);
            $node = $node->parent;
        }

        return view('filebox.index', [
            'files'         => $files,
            'currentFolder' => $currentFolder,
            'breadcrumbs'   => $breadcrumbs,
        ]);
    }

    public function store(Request $request)
    {
        $this->assertRole(); // admin/teacher のみ

        $request->validate([
            'file'         => 'required|file|max:20480', // 20MB
            'parent_id'    => 'nullable|exists:shared_files,id',
            'is_protected' => 'nullable|boolean',
            'password'     => 'nullable|string|min:4',
        ]);

        $parentPath = 'filebox';
        $parentId = $request->input('parent_id');

        if ($parentId) {
            $parent = SharedFile::where('type', 'folder')->findOrFail($parentId);
            $parentPath = $parent->file_path;
        }

        $origName = $request->file('file')->getClientOriginalName();
        $safeName = $this->sanitizeName($origName);
        if ($safeName === '') {
            return back()->withErrors(['file' => '不正なファイル名です。']);
        }

        // 同名重複時は (n) 付与
        $targetPath = $parentPath . '/' . $safeName;
        $targetPath = $this->uniquify($targetPath);

        // 指定ファイル名で保存
        $stored = $request->file('file')->storeAs($parentPath, basename($targetPath), 'public');

        SharedFile::create([
            'user_id'      => Auth::id(),
            'file_name'    => basename($targetPath),
            'file_path'    => $stored,
            'type'         => 'file',
            'parent_id'    => $parentId,
            'is_protected' => $request->boolean('is_protected'),
            'password'     => $request->filled('password') ? bcrypt($request->password) : null,
        ]);

        return back()->with('success', 'ファイルをアップロードしました。');
    }

    public function storeFolder(Request $request)
    {
        $this->assertRole(); // admin/teacher のみ

        $request->validate([
            'folder_name' => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:shared_files,id',
        ]);

        $parentPath = 'filebox';
        $parentId = $request->input('parent_id');

        if ($parentId) {
            $parent = SharedFile::where('type', 'folder')->findOrFail($parentId);
            $parentPath = $parent->file_path;
        }

        $safeName = $this->sanitizeName($request->folder_name, true);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            return back()->withErrors(['folder' => '不正なフォルダ名です。']);
        }

        $folderPath = $parentPath . '/' . $safeName;

        if (Storage::disk('public')->exists($folderPath)) {
            return back()->withErrors(['folder' => '同名のフォルダが既に存在します。']);
        }

        Storage::disk('public')->makeDirectory($folderPath);

        SharedFile::create([
            'user_id'   => Auth::id(),
            'file_name' => $safeName,
            'file_path' => $folderPath,
            'type'      => 'folder',
            'parent_id' => $parentId,
        ]);

        return back()->with('success', 'フォルダを作成しました。');
    }

    public function rename(Request $request, SharedFile $sharedFile)
    {
        $this->assertRole(); // admin/teacher のみ

        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $oldPath = $sharedFile->file_path;
        $dir     = dirname($oldPath);
        $safe    = $this->sanitizeName($request->file_name, $sharedFile->type === 'folder');

        if ($safe === '' || $safe === '.' || $safe === '..') {
            return back()->withErrors(['file_name' => '不正な名前です。']);
        }

        $newPath = $dir . '/' . $safe;

        if ($newPath !== $oldPath && Storage::disk('public')->exists($newPath)) {
            return back()->withErrors(['file_name' => '同名のファイル/フォルダが既に存在します。']);
        }

        Storage::disk('public')->move($oldPath, $newPath);

        $sharedFile->update([
            'file_name' => $safe,
            'file_path' => $newPath,
        ]);

        return back()->with('success', '名前を変更しました。');
    }

    public function download(Request $request, $id)
    {
        $file = SharedFile::findOrFail($id);

        if ($file->type !== 'file') {
            abort(404);
        }

        if ($file->is_protected) {
            $request->validate([
                'password' => 'required|string',
            ]);
            if (!password_verify($request->password, $file->password)) {
                return back()->withErrors(['password' => 'パスワードが違います。']);
            }
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function destroy(SharedFile $sharedFile)
    {
        $this->assertRole(); // admin/teacher のみ

        if ($sharedFile->type === 'folder') {
            Storage::disk('public')->deleteDirectory($sharedFile->file_path);
        } else {
            if (Storage::disk('public')->exists($sharedFile->file_path)) {
                Storage::disk('public')->delete($sharedFile->file_path);
            }
        }

        $sharedFile->delete();

        return back()->with('success', '削除しました。');
    }

    /** ロール検査（admin / teacher 以外は 403） */
    private function assertRole(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'teacher'], true)) {
            abort(403);
        }
    }

    /**
     * ファイル/フォルダ名の無害化
     * - スラッシュ、制御文字、危険記号を除去
     * - 先頭末尾のドット・空白をトリム
     */
    private function sanitizeName(string $name, bool $isFolder = false): string
    {
        $name = preg_replace('/[\\x00-\\x1F\\x7F\\/\\\\:*?"<>|]/u', '', $name) ?? '';
        $name = trim($name, " .\t\n\r\0\x0B");
        // フォルダ名で末尾のドット・スペースは不可
        if ($isFolder) {
            $name = rtrim($name, " .");
        }
        return $name;
    }

    /** パス重複時に (n) を付与して一意化 */
    private function uniquify(string $targetPath): string
    {
        $disk = Storage::disk('public');
        if (!$disk->exists($targetPath)) {
            return $targetPath;
        }
        $dir  = dirname($targetPath);
        $base = pathinfo($targetPath, PATHINFO_FILENAME);
        $ext  = pathinfo($targetPath, PATHINFO_EXTENSION);
        $n = 1;
        do {
            $candidate = $dir . '/' . $base . " ({$n})" . ($ext ? ".{$ext}" : '');
            $n++;
        } while ($disk->exists($candidate));
        return $candidate;
    }
}
