
<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block font-medium">生徒</label>
        <select name="student_id" class="w-full border rounded p-2" required>
            <option value="">選択してください</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}"
                    {{ old('student_id', $mockTest->student_id ?? '') == $student->id ? 'selected' : '' }}>
                    {{ $student->last_name }} {{ $student->first_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">学年</label>
            <input type="number" name="grade" value="{{ old('grade', $mockTest->grade ?? '') }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block font-medium">回数</label>
            <input type="number" name="exam_number" value="{{ old('exam_number', $mockTest->exam_number ?? '') }}" class="w-full border rounded p-2" required>
        </div>
    </div>

    @foreach (['japanese' => '国語', 'math' => '数学', 'english' => '英語', 'science' => '理科', 'social' => '社会'] as $key => $label)
        <div>
            <label class="block font-medium">{{ $label }}（点数）</label>
            <input type="number" name="{{ $key }}" value="{{ old($key, $mockTest->$key ?? '') }}" class="w-full border rounded p-2" required>
        </div>
    @endforeach
    <hr class="my-4">

    <h3 class="text-lg font-semibold">偏差値</h3>

    @foreach ([
        'japanese_deviation' => '国語偏差値',
        'math_deviation' => '数学偏差値',
        'english_deviation' => '英語偏差値',
        'science_deviation' => '理科偏差値',
        'social_deviation' => '社会偏差値',
    ] as $key => $label)
        <div>
            <label class="block font-medium">{{ $label }}</label>
            <input type="number" step="0.1" name="{{ $key }}" value="{{ old($key, $mockTest->$key ?? '') }}" class="w-full border rounded p-2">
        </div>
    @endforeach

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">3教科偏差値</label>
            <input type="number" step="0.1" name="three_subjects_deviation" value="{{ old('three_subjects_deviation', $mockTest->three_subjects_deviation ?? '') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block font-medium">5教科偏差値</label>
            <input type="number" step="0.1" name="five_subjects_deviation" value="{{ old('five_subjects_deviation', $mockTest->five_subjects_deviation ?? '') }}" class="w-full border rounded p-2">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">3教科合計</label>
            <input type="number" name="three_subjects_total" value="{{ old('three_subjects_total', $mockTest->three_subjects_total ?? '') }}" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block font-medium">5教科合計</label>
            <input type="number" name="five_subjects_total" value="{{ old('five_subjects_total', $mockTest->five_subjects_total ?? '') }}" class="w-full border rounded p-2" required>
        </div>
    </div>
</div>

