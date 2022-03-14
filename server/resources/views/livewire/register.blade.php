<div>
    <form wire:submit.prevent="register">
        <label for="name">名前</label>
        <input id="name" type="text" wire:model="name" /><br>

        <label for="email">メールアドレス</label>
        <input type="text" id="email" wire:model="email"><br>

        <label for="password">パスワード</label>
        <input type="password" id="password" wire:model="password"><br>
        <button>登録する</button>
    </form>
</div>
