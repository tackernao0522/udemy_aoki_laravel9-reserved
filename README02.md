# セクション 02: Jetstream, Fortify

## 10 Jetstream の紹介

### 認証ライブラリ比較

|              |                                 Laravel / ui                                 |                                Laravel Breeze                                |                   Fortify                    |       Jetstream       |
| :----------: | :--------------------------------------------------------------------------: | :--------------------------------------------------------------------------: | :------------------------------------------: | :-------------------: |
|   Version    |                                    6.x〜                                     |                                    8.x〜                                     |                    8.x〜                     |         8.x〜         |
| View（PHP）  |                                    Blade                                     |                                    Blade                                     |                      -                       |   Livewire + Blade    |
|      JS      |                              Vue.js / React.js                               |                                  Alpine.js                                   |                      -                       |  Inertia.js + Vue.js  |
|     CSS      |                                  Bootstrap                                   |                                 Tailwindcss                                  |                      -                       |      Tailwindcss      |
| 追加ファイル |                            View/COntroller/Route                             |                            View/Controller/Route                             |                      -                       | View/Controller/Route |
|    機能 1    | ログイン、ユーザー登録、パスワードのリセット、<br>メール検証、パスワード確認 | ログイン、ユーザー登録、パスワードのリセット、<br>メール検証、パスワード確認 |                      -                       |
|    機能 2    |                                      -                                       |                                      -                                       | 2 要素認証、<br>プロフィール管理、チーム管理 | API サポート(Sanctum) |

### Jetstream について

Laravel Fortify ・・セッションベースの認証<br>

Laravel Sanctum ・・ ユーザプローフィール・チーム管理周りの UI のビュー<br>

Tailwind CSS ・・ UI のデザイン<br>

Jetstream 自体 ・・ ルートやビュー、コントローラのスカフォールド等を担当<br>
