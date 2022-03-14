## 25 アクション wire:click, wire:mouseover など

### アクション

`wire:click=""` または `@click=""`<br>

`wire:keydown=""` `wire:keydown.enter=""`<br>

`wire:mouseover=""`<br>

`wire:submit.prenent=""` ページ読み込みを防ぐ<br>

JavaScript のアクション(イベント)と同様<br>
https://developer.mozilla.org/ja/docs/Web/Events <br>

クラス<br>

```
public function mouseOver()
{
  $this->name = 'mouseover';
}
```

ビュー<br>

```
<button wire:mouseover="mouseover">マウスを合わせてね</button>
```

### ハンズオン

- `app/Http/Livewire/Counter.php`を編集<br>

```php:Counter.php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
  public $count = 0;
  public $name = '';

  public function mount()
  {
    $this->name = 'mount';
  }

  public function updated()
  {
    $this->name = 'updated';
  }

  public function mouseOver()
  {
    $this->name = 'mouseover';
  }

  public function increment()
  {
    $this->count++;
  }

  public function render()
  {
    return view('livewire.counter');
  }
}
```

- `resources/views/livewire/counter.blade.php`を編集<br>

```html:counter.blade.php
<div style="text-align: center">
  <button wire:click="increment">+</button>
  <h1>{{ $count }}</h1>
  <div class="mb-8"></div>
  こんにちは、{{ $name }} さん
  <br />
  <input type="text" wire:model="name" />
  {{--
  <input type="text" wire:model.debounce.2000ms="name" />
  --}} {{--
  <input type="text" wire:model.lazy="name" />
  --}} {{--
  <input type="text" wire:model.defer="name" />
  --}}
  <br />
  <button wire:mouseover="mouseOver">マウスを合わせてね</button>
</div>
```

- 参考: https://readouble.com/livewire/2.x/ja/actions.html <br>
