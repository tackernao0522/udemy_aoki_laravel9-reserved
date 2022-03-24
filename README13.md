## 58 バリデーション

https://readouble.com/laravel/9.x/ja/validation.html (使用可能なバリデーションルール)<br>

### バリデーション 日本語化

`lang/ja/validation.php`<br>

```php:validation.php
'attributes' => [
  'email' => 'メールアドレス',
  'password' => 'パスワード',
  'name' => '名前',
  'event_name' => 'イベント名',
  'information' => 'イベント詳細',
  'event_date' => 'イベントの日付',
  'end_time' => '終了時間',
  'start_time' => '開始時間',
  'max_people' => '定員',
],
```

### メッセージの日本語化

`lang/ja.json`<br>

```json:ja.json
{ "Whoops! Something went wrong.": "問題が発生しました。" }
```

### フォームリクエスト

`app/Http/Requests/StoreEventRequest.php`<br>

```php:StoreEventRequest.php
public function authorize()
{
  return true;
}

public function rules()
{
  return [
    'event_name' => ['required', 'max:50'],
    'information' => ['required', 'max:200'],
    'event_date' => ['required', 'date'],
    'start_time' => ['required'],
    'end_time' => ['required', 'after:start_time'], // 開始時間よりも後でなければ引っかかる
    'max_people' => ['required', 'numeric', 'between:1, 20'],
    'is_visible' => ['required', 'boolean'],
  ];
}
```

### ハンズオン

- `lang/ja/validation.php`を編集<br>

```php:validation.php
<?php

return [
  /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行はバリデタークラスにより使用されるデフォルトのエラー
    | メッセージです。サイズルールのようにいくつかのバリデーションを
    | 持っているものもあります。メッセージはご自由に調整してください。
    |
    */

  'accepted' => ':attributeを承認してください。',
  'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
  'active_url' => ':attributeが有効なURLではありません。',
  'after' => ':attributeには、:dateより後の日付を指定してください。',
  'after_or_equal' => ':attributeには、:date以降の日付を指定してください。',
  'alpha' => ':attributeはアルファベットのみがご利用できます。',
  'alpha_dash' =>
    ':attributeはアルファベットとダッシュ(-)及び下線(_)がご利用できます。',
  'alpha_num' => ':attributeはアルファベット数字がご利用できます。',
  'array' => ':attributeは配列でなくてはなりません。',
  'before' => ':attributeには、:dateより前の日付をご利用ください。',
  'before_or_equal' => ':attributeには、:date以前の日付をご利用ください。',
  'between' => [
    'numeric' => ':attributeは、:minから:maxの間で指定してください。',
    'file' => ':attributeは、:min kBから、:max kBの間で指定してください。',
    'string' => ':attributeは、:min文字から、:max文字の間で指定してください。',
    'array' => ':attributeは、:min個から:max個の間で指定してください。',
  ],
  'boolean' => ':attributeは、trueかfalseを指定してください。',
  'confirmed' => ':attributeと、確認フィールドとが、一致していません。',
  'current_password' => 'パスワードが正しくありません。',
  'date' => ':attributeには有効な日付を指定してください。',
  'date_equals' => ':attributeには、:dateと同じ日付けを指定してください。',
  'date_format' => ':attributeは:format形式で指定してください。',
  'different' => ':attributeと:otherには、異なった内容を指定してください。',
  'digits' => ':attributeは:digits桁で指定してください。',
  'digits_between' => ':attributeは:min桁から:max桁の間で指定してください。',
  'dimensions' => ':attributeの図形サイズが正しくありません。',
  'distinct' => ':attributeには異なった値を指定してください。',
  'email' => ':attributeには、有効なメールアドレスを指定してください。',
  'ends_with' =>
    ':attributeには、:valuesのどれかで終わる値を指定してください。',
  'exists' => '選択された:attributeは正しくありません。',
  'file' => ':attributeにはファイルを指定してください。',
  'filled' => ':attributeに値を指定してください。',
  'gt' => [
    'numeric' => ':attributeには、:valueより大きな値を指定してください。',
    'file' => ':attributeには、:value kBより大きなファイルを指定してください。',
    'string' => ':attributeは、:value文字より長く指定してください。',
    'array' => ':attributeには、:value個より多くのアイテムを指定してください。',
  ],
  'gte' => [
    'numeric' => ':attributeには、:value以上の値を指定してください。',
    'file' => ':attributeには、:value kB以上のファイルを指定してください。',
    'string' => ':attributeは、:value文字以上で指定してください。',
    'array' => ':attributeには、:value個以上のアイテムを指定してください。',
  ],
  'image' => ':attributeには画像ファイルを指定してください。',
  'in' => '選択された:attributeは正しくありません。',
  'in_array' => ':attributeには:otherの値を指定してください。',
  'integer' => ':attributeは整数で指定してください。',
  'ip' => ':attributeには、有効なIPアドレスを指定してください。',
  'ipv4' => ':attributeには、有効なIPv4アドレスを指定してください。',
  'ipv6' => ':attributeには、有効なIPv6アドレスを指定してください。',
  'json' => ':attributeには、有効なJSON文字列を指定してください。',
  'lt' => [
    'numeric' => ':attributeには、:valueより小さな値を指定してください。',
    'file' => ':attributeには、:value kBより小さなファイルを指定してください。',
    'string' => ':attributeは、:value文字より短く指定してください。',
    'array' => ':attributeには、:value個より少ないアイテムを指定してください。',
  ],
  'lte' => [
    'numeric' => ':attributeには、:value以下の値を指定してください。',
    'file' => ':attributeには、:value kB以下のファイルを指定してください。',
    'string' => ':attributeは、:value文字以下で指定してください。',
    'array' => ':attributeには、:value個以下のアイテムを指定してください。',
  ],
  'max' => [
    'numeric' => ':attributeには、:max以下の数字を指定してください。',
    'file' => ':attributeには、:max kB以下のファイルを指定してください。',
    'string' => ':attributeは、:max文字以下で指定してください。',
    'array' => ':attributeは:max個以下指定してください。',
  ],
  'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
  'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
  'min' => [
    'numeric' => ':attributeには、:min以上の数字を指定してください。',
    'file' => ':attributeには、:min kB以上のファイルを指定してください。',
    'string' => ':attributeは、:min文字以上で指定してください。',
    'array' => ':attributeは:min個以上指定してください。',
  ],
  'multiple_of' => ':attributeには、:valueの倍数を指定してください。',
  'not_in' => '選択された:attributeは正しくありません。',
  'not_regex' => ':attributeの形式が正しくありません。',
  'numeric' => ':attributeには、数字を指定してください。',
  'password' => '正しいパスワードを指定してください。',
  'present' => ':attributeが存在していません。',
  'regex' => ':attributeに正しい形式を指定してください。',
  'required' => ':attributeは必ず指定してください。',
  'required_if' => ':otherが:valueの場合、:attributeも指定してください。',
  'required_unless' =>
    ':otherが:valuesでない場合、:attributeを指定してください。',
  'required_with' => ':valuesを指定する場合は、:attributeも指定してください。',
  'required_with_all' =>
    ':valuesを指定する場合は、:attributeも指定してください。',
  'required_without' =>
    ':valuesを指定しない場合は、:attributeを指定してください。',
  'required_without_all' =>
    ':valuesのどれも指定しない場合は、:attributeを指定してください。',
  'prohibited' => ':attributeは入力禁止です。',
  'prohibited_if' => ':otherが:valueの場合、:attributeは入力禁止です。',
  'prohibited_unless' => ':otherが:valueでない場合、:attributeは入力禁止です。',
  'prohibits' => 'attributeは:otherの入力を禁じています。',
  'same' => ':attributeと:otherには同じ値を指定してください。',
  'size' => [
    'numeric' => ':attributeは:sizeを指定してください。',
    'file' => ':attributeのファイルは、:sizeキロバイトでなくてはなりません。',
    'string' => ':attributeは:size文字で指定してください。',
    'array' => ':attributeは:size個指定してください。',
  ],
  'starts_with' =>
    ':attributeには、:valuesのどれかで始まる値を指定してください。',
  'string' => ':attributeは文字列を指定してください。',
  'timezone' => ':attributeには、有効なゾーンを指定してください。',
  'unique' => ':attributeの値は既に存在しています。',
  'uploaded' => ':attributeのアップロードに失敗しました。',
  'url' => ':attributeに正しい形式を指定してください。',
  'uuid' => ':attributeに有効なUUIDを指定してください。',

  /*
    |--------------------------------------------------------------------------
    | Custom バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | "属性.ルール"の規約でキーを指定することでカスタムバリデーション
    | メッセージを定義できます。指定した属性ルールに対する特定の
    | カスタム言語行を手早く指定できます。
    |
    */

  'custom' => [
    '属性名' => [
      'ルール名' => 'カスタムメッセージ',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性名
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、例えば"email"の代わりに「メールアドレス」のように、
    | 読み手にフレンドリーな表現でプレースホルダーを置き換えるために指定する
    | 言語行です。これはメッセージをよりきれいに表示するために役に立ちます。
    |
    */

  // 編集
  'attributes' => [
    'email' => 'メールアドレス',
    'password' => 'パスワード',
    'name' => '名前',
    'event_name' => 'イベント名',
    'information' => 'イベント詳細',
    'event_date' => 'イベントの日付',
    'start_time' => '開始時間',
    'end_time' => '終了時間',
    'max_people' => '定員',
  ],
];
```

- `$ touch lang/ja.json`を実行<br>

* `lang/ja.json`を編集<br>

```json:ja.json
{ "Whoops! Something went wrong.": "問題が発生しました。" }
```

- `app/Http/Requests/StoreEventRequest.php`を編集<br>

```php:StoreEventRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  // 編集
  public function rules()
  {
    return [
      'event_name' => ['required', 'max:50'],
      'information' => ['required', 'max:200'],
      'event_date' => ['required', 'date'],
      'start_time' => ['required'],
      'end_time' => ['required', 'after:start_time'], // 開始時間よりも後でなければ引っかかる
      'max_people' => ['required', 'numeric', 'between:1, 20'],
      'is_visible' => ['required', 'boolean'],
    ];
  }
}
```

## 59 保存処理(日付と時間の結合)

### モデル

`Event::create()`で保存できるようにするためにモデルに追記<br>
(DB テーブルの列名)<br>

`app/Models/Event.php`<br>

```php:Event.php
protected $fillable = [
  'name',
  'information',
  'max_people',
  'start_date',
  'end_date',
  'is_visible',
];
```

### EventController@store

```php:EventController.php
// formatは event_date, start_time, end_time modelはstart_date, end_date
$start = $request['event_date'] . ' ' . $request['start_time'];
$start_date = Carbon::createFromFormat('Y-m-d H:i', $start);

Event::create([
  'name' => $request['event_name'],
  'information' => $request['information'],
  'start_date' => $start_date,
  'end_date' => $end_date,
  'max_people' => $request['max_people'],
  'is_visible' => $request['is_visible'],
]);

session()->flash('status', '登録okです');

return to_route('events.index'); // 名前付きルート
```

### ハンズオン

- `app/Models/Event.php`を編集<br>

```php:Event.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  use HasFactory;

  // 追加
  protected $fillable = [
    'name',
    'information',
    'max_people',
    'start_date',
    'end_date',
    'is_visible',
  ];
}
```

- `app/Http/Controllers/EventController.php`を編集<br>

```php:EventController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
// 追加
use App\Models\Event;
use Illuminate\Support\Carbon;
// ここまで
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $events = DB::table('events')
      ->orderBy('start_date', 'ASC')
      ->paginate(10);

    return view('manager.events.index', compact('events'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('manager.events.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreEventRequest  $request
   * @return \Illuminate\Http\Response
   */
  // 編集
  public function store(StoreEventRequest $request)
  {
    // dd($request);
    // formatは event_date, start_time, end_time modelはstart_date, end_date
    $start = $request['event_date'] . ' ' . $request['start_time'];
    $startDate = Carbon::createFromFormat('Y-m-d H:i', $start);

    $end = $request['event_date'] . ' ' . $request['end_time'];
    $endDate = Carbon::createFromFormat('Y-m-d H:i', $end);

    Event::create([
      'name' => $request['event_name'],
      'information' => $request['information'],
      'start_date' => $startDate,
      'end_date' => $endDate,
      'max_people' => $request['max_people'],
      'is_visible' => $request['is_visible'],
    ]);

    session()->flash('status', '登録okです');

    return to_route('events.index'); // 名前付きルート
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function show(Event $event)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function edit(Event $event)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateEventRequest  $request
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateEventRequest $request, Event $event)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function destroy(Event $event)
  {
    //
  }
}
```

- `resources/views/manager/events/create.blade.php`を編集<br>

```php:create.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント新規登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-2xl py-4 mx-auto">
                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('events.store') }}">
                        @csrf

                        <div>
                            <x-jet-label for="event_name" value="イベント名" />
                            <x-jet-input id="event_name" class="block mt-1 w-full" type="text" name="event_name"
                                :value="old('event_name')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-jet-label for="information" value="イベント詳細" />
                            // 編集 name属性を入れる
                            <x-textarea row="3" id="information" name="information" class="block mt-1 w-full">{{ old('information') }}
                            </x-textarea>
                        </div>

                        <div class="md:flex justify-between">
                            <div class="mt-4">
                                <x-jet-label for="event_date" value="イベント日付" />
                                <x-jet-input id="event_date" class="block mt-1 w-full" type="text" name="event_date"
                                    required />
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="start_time" value="開始時間" />
                                <x-jet-input id="start_time" class="block mt-1 w-full" type="text" name="start_time"
                                    required />
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="end_time" value="終了時間" />
                                <x-jet-input id="end_time" class="block mt-1 w-full" type="text" name="end_time"
                                    required />
                            </div>
                        </div>
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label for="max_people" value="定員数" />
                                <x-jet-input id="max_people" class="block mt-1 w-full" type="number" name="max_people"
                                    required />
                            </div>
                            <div class="flex space-x-4 justify-around">
                                <input type="radio" name="is_visible" value="1" checked />表示
                                <input type="radio" name="is_visible" value="0" />非表示
                            </div>
                            <x-jet-button class="ml-4">
                                新規登録
                            </x-jet-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```

- `resources/views/manager/events/index.blade.php`を編集<br>

```php:index.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント管理
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-4 mx-auto">
                        // 追加
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif
                        // ここまで
                        <button onclick="location.href='{{ route('events.create') }}'"
                            class="flex mb-4 ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">新規登録</button>
                        <div class="w-full mx-auto overflow-auto">
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            イベント名</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            開始日時</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            終了日時</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            予約人数</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            定員</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            表示・非表示</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $event)
                                        <tr>
                                            <td class="px-4 py-3">{{ $event->name }}</td>
                                            <td class="px-4 py-3">{{ $event->start_date }}</td>
                                            <td class="px-4 py-3">{{ $event->end_date }}</td>
                                            <td class="px-4 py-3">後程対応</td>
                                            <td class="px-4 py-3">{{ $event->max_people }}</td>
                                            <td class="px-4 py-3">{{ $event->is_visible }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $events->links() }}
                        </div>
                        <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
```
