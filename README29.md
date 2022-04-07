## 100 満員時の表示変更

### 満員時の表記

```php:event-detail.blade.php
@if($reservablePeople <= 0)
  <span class="text-xs">このイベントは満員です。</span>
@else
  <x-jet-label for="reserved_people" value="予約人数" />
  <select name="reserved_people">
    @for($i = 1; $i <= $reservablePeople; $i++)
      <option value="{{ $i }}">{{ $i }}</option>
    @endfor
  </select>
@endif
```

### ハンズオン

- イベント満員時はカレンダー表示色を変える<br>

* イベント予約時にメール送信<br>

- キャンセル待ち機能(キャンセルがでたらメール通知)<br>
