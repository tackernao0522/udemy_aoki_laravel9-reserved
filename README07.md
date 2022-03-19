# セクション 05: 施設側(manager) その 1

## 41 今回の予約システムについて

### 予約管理システム

サロン、病院、スタジオ(音楽・フィットネス)<br>
セミナー、スクール、イベント、
公共施設、ホテル、飲食店、会議室、ゴルフ場、オンライン英会話<br>
などなど<br>

### スポーツジム内のスタジオを参考

スポーツジム内にダンススタジオが 1 つ<br>

ヨガ、キックボクシング、Zumba、ダンス、エアロビ・・<br>
1 つのスタジオで日によって複数のイベントが開催されている<br>

### 予約システムの仕様

施設内にスタジオが 1 つ<br>

使用例<br>
料理教室、オンラインセミナー、野外イベント など<br>

出来ない事<br>
(同じ時間帯に複数のイベントは作成できない)<br>
会議室(1 施設内に複数予約できるスペースがある)<br>

### 予約システムの仕様

座席指定はしない(先着順)<br>

座席指定のサンプル<br>
映画館、航空機<br>

## 42 アプリ名、ロゴ設定

アプリ名・・.env ファイル<br>

```
APP_NAME=uReserve
```

`config/app.php`内で設定される<br>

ロゴ(ロゴ 作成 無料 などで検索)<br>
https://drive.google.com/file/d/1-hp45OYG3M2ivxWKWJVcSP1oK6xErOdN/view <br>

### ハンズオン

- `.env`を編集<br>

```:.env
APP_NAME=uReserve # 編集
APP_ENV=local
APP_KEY=base64:yl4ha2RgJmxIOOVDia1384AphZGOtRz8+TR0NPS31Vo=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=laravel9reserveddb-host
DB_PORT=3306
DB_DATABASE=laravl9reserved-database
DB_USERNAME=laravel9_reserved
DB_PASSWORD=5t5a7k3a

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

#Sendgrid用
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=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
MAIL_ENCRYPTION=tls
MAIL_FROM_NAME=Demo_funclub
MAIL_FROM_ADDRESS=takaki_5573031@yahoo.co.jp

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

- `キャッシュをクリアしておく`<br>

### ロゴ表示

public フォルダに直接置く・・初期ファイル<br>
storage フォルダ・・フォルダ内画像は gitHub にアップしない<br>

表側(public)から見られるようにリンク<br>
php artisan storage:link<br>
public/storage リンクが生成される<br>

asset() ヘルパ関数で public 内のファイルを指定<br>

asset("images/logo.png")を<br>
vendor/jetstream/application-mark.blade.php<br>
vendor/jetstream/authentication-card-logo.blade.php に記載<br>

### ハンズオン

- `public/images`ディレクトリを作成<br>

* `public/images`ディレクトリに`logo.png`ファイルを配置<br>

- `resources/views/vendor/jetstream/components/authentication-card-logo.blade.php`を編集<br>

```html:authentication-card-logo.blade.php
<img src="{{ asset('images/logo.png')}}" />
```

- `resources/views/auth/login.blade.php`を編集<br>

```html:login.blade.php
<x-guest-layout>
  <x-jet-authentication-card>
    <x-slot name="logo">
      <!-- 追加 -->
      <div class="w-40">
        <x-jet-authentication-card-logo />
      </div>
      <!-- ここまで -->
    </x-slot>

    <x-jet-validation-errors class="mb-4" />

    @if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
      {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div>
        <x-jet-label for="email" value="{{ __('Email') }}" />
        <x-jet-input
          id="email"
          class="block mt-1 w-full"
          type="email"
          name="email"
          :value="old('email')"
          required
          autofocus
        />
      </div>

      <div class="mt-4">
        <x-jet-label for="password" value="{{ __('Password') }}" />
        <x-jet-input
          id="password"
          class="block mt-1 w-full"
          type="password"
          name="password"
          required
          autocomplete="current-password"
        />
      </div>

      <div class="block mt-4">
        <label for="remember_me" class="flex items-center">
          <x-jet-checkbox id="remember_me" name="remember" />
          <span class="ml-2 text-sm text-gray-600">
            {{ __('Remember me') }}
          </span>
        </label>
      </div>

      <div class="flex items-center justify-end mt-4">
        @if (Route::has('password.request'))
        <a
          class="underline text-sm text-gray-600 hover:text-gray-900"
          href="{{ route('password.request') }}"
        >
          {{ __('Forgot your password?') }}
        </a>
        @endif

        <x-jet-button class="ml-4">
          {{ __('Log in') }}
        </x-jet-button>
      </div>
    </form>
  </x-jet-authentication-card>
</x-guest-layout>
```

- `resources/views/vendor/jeststream/components/application-mark.blade.php`を編集<br>

```html:application-mark.blade.php
<img src="{{ asset('images/logo.png')}}" />
```

- `resources/views/navigation-menu.blade.php`を編集<br>

```html:navigation-menu.blade.php
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
  <!-- Primary Navigation Menu -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        <!-- Logo -->
        <!-- 編集 -->
        <div class="w-20 shrink-0 flex items-center">
          <a href="{{ route('dashboard') }}">
            <x-jet-application-mark class="block h-9 w-auto" />
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
          <x-jet-nav-link
            href="{{ route('dashboard') }}"
            :active="request()->routeIs('dashboard')"
          >
            {{ __('Dashboard') }}
          </x-jet-nav-link>
        </div>
      </div>

      <div class="hidden sm:flex sm:items-center sm:ml-6">
        <!-- Teams Dropdown -->
        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <div class="ml-3 relative">
          <x-jet-dropdown align="right" width="60">
            <x-slot name="trigger">
              <span class="inline-flex rounded-md">
                <button
                  type="button"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition"
                >
                  {{ Auth::user()->currentTeam->name }}

                  <svg
                    class="ml-2 -mr-0.5 h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </span>
            </x-slot>

            <x-slot name="content">
              <div class="w-60">
                <!-- Team Management -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                  {{ __('Manage Team') }}
                </div>

                <!-- Team Settings -->
                <x-jet-dropdown-link
                  href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                >
                  {{ __('Team Settings') }}
                </x-jet-dropdown-link>

                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                <x-jet-dropdown-link href="{{ route('teams.create') }}">
                  {{ __('Create New Team') }}
                </x-jet-dropdown-link>
                @endcan

                <div class="border-t border-gray-100"></div>

                <!-- Team Switcher -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                  {{ __('Switch Teams') }}
                </div>

                @foreach (Auth::user()->allTeams() as $team)
                <x-jet-switchable-team :team="$team" />
                @endforeach
              </div>
            </x-slot>
          </x-jet-dropdown>
        </div>
        @endif

        <!-- Settings Dropdown -->
        <div class="ml-3 relative">
          <x-jet-dropdown align="right" width="48">
            <x-slot name="trigger">
              @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
              <button
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
              >
                <img
                  class="h-8 w-8 rounded-full object-cover"
                  src="{{ Auth::user()->profile_photo_url }}"
                  alt="{{ Auth::user()->name }}"
                />
              </button>
              @else
              <span class="inline-flex rounded-md">
                <button
                  type="button"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                >
                  {{ Auth::user()->name }}

                  <svg
                    class="ml-2 -mr-0.5 h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </span>
              @endif
            </x-slot>

            <x-slot name="content">
              <!-- Account Management -->
              <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Manage Account') }}
              </div>

              <x-jet-dropdown-link href="{{ route('profile.show') }}">
                {{ __('Profile') }}
              </x-jet-dropdown-link>

              @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
              <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                {{ __('API Tokens') }}
              </x-jet-dropdown-link>
              @endif

              <div class="border-t border-gray-100"></div>

              <!-- Authentication -->
              <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf

                <x-jet-dropdown-link
                  href="{{ route('logout') }}"
                  @click.prevent="$root.submit();"
                >
                  {{ __('Log Out') }}
                </x-jet-dropdown-link>
              </form>
            </x-slot>
          </x-jet-dropdown>
        </div>
      </div>

      <!-- Hamburger -->
      <div class="-mr-2 flex items-center sm:hidden">
        <button
          @click="open = ! open"
          class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
        >
          <svg
            class="h-6 w-6"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 24 24"
          >
            <path
              :class="{'hidden': open, 'inline-flex': ! open }"
              class="inline-flex"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
            <path
              :class="{'hidden': ! open, 'inline-flex': open }"
              class="hidden"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Responsive Navigation Menu -->
  <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
      <x-jet-responsive-nav-link
        href="{{ route('dashboard') }}"
        :active="request()->routeIs('dashboard')"
      >
        {{ __('Dashboard') }}
      </x-jet-responsive-nav-link>
    </div>

    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200">
      <div class="flex items-center px-4">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div class="shrink-0 mr-3">
          <img
            class="h-10 w-10 rounded-full object-cover"
            src="{{ Auth::user()->profile_photo_url }}"
            alt="{{ Auth::user()->name }}"
          />
        </div>
        @endif

        <div>
          <div class="font-medium text-base text-gray-800">
            {{ Auth::user()->name }}
          </div>
          <div class="font-medium text-sm text-gray-500">
            {{ Auth::user()->email }}
          </div>
        </div>
      </div>

      <div class="mt-3 space-y-1">
        <!-- Account Management -->
        <x-jet-responsive-nav-link
          href="{{ route('profile.show') }}"
          :active="request()->routeIs('profile.show')"
        >
          {{ __('Profile') }}
        </x-jet-responsive-nav-link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
        <x-jet-responsive-nav-link
          href="{{ route('api-tokens.index') }}"
          :active="request()->routeIs('api-tokens.index')"
        >
          {{ __('API Tokens') }}
        </x-jet-responsive-nav-link>
        @endif

        <!-- Authentication -->
        <form method="POST" action="{{ route('logout') }}" x-data>
          @csrf

          <x-jet-responsive-nav-link
            href="{{ route('logout') }}"
            @click.prevent="$root.submit();"
          >
            {{ __('Log Out') }}
          </x-jet-responsive-nav-link>
        </form>

        <!-- Team Management -->
        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <div class="border-t border-gray-200"></div>

        <div class="block px-4 py-2 text-xs text-gray-400">
          {{ __('Manage Team') }}
        </div>

        <!-- Team Settings -->
        <x-jet-responsive-nav-link
          href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
          :active="request()->routeIs('teams.show')"
        >
          {{ __('Team Settings') }}
        </x-jet-responsive-nav-link>

        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
        <x-jet-responsive-nav-link
          href="{{ route('teams.create') }}"
          :active="request()->routeIs('teams.create')"
        >
          {{ __('Create New Team') }}
        </x-jet-responsive-nav-link>
        @endcan

        <div class="border-t border-gray-200"></div>

        <!-- Team Switcher -->
        <div class="block px-4 py-2 text-xs text-gray-400">
          {{ __('Switch Teams') }}
        </div>

        @foreach (Auth::user()->allTeams() as $team)
        <x-jet-switchable-team
          :team="$team"
          component="jet-responsive-nav-link"
        />
        @endforeach @endif
      </div>
    </div>
  </div>
</nav>
```
