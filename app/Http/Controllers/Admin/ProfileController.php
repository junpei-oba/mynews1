<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// 課題1(php-16)
// ProfileControllerを編集して、admin/profile/createから送信されてきたフォーム情報を
// データベースに保存するようにしましょう。(ヒント: NewsController 参照)
// 以下を追記することでNews Modelが扱えるようになる
use App\Profile;

// 課題2(php-17⑹)
use App\ProfileHistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function add()
    {
        return view('admin.profile.create');
    }
    public function create(Request $request)
    {
        // 以下を追記
      // Varidationを行う
      $this->validate($request, Profile::$rules);

      $profile = new Profile;
      $form = $request->all();

      // フォームから送信されてきた_tokenを削除する
      unset($form['_token']);
      // フォームから送信されてきたimageを削除する
      unset($form['image']);

      // データベースに保存する
      $profile->fill($form);
      $profile->save();
      
        // admin/profile/createにリダイレクトする
        return redirect('admin/profile/create');   
    }
      public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          // 検索されたら検索結果を取得する
          $posts = Profile::where('title', $cond_title)->get();
      } else {
          // それ以外はすべてのプロフィールを取得する
          $posts = Profile::all();
      }
      return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }
    public function edit(Request $request)
    {
      // Profile Modelからデータを取得する
      $profile = Profile::find($request->id);
      if (empty($profile)) {
        abort(404);    
      }
      return view('admin.profile.edit', ['profile_form' => $profile ]);
    }
    public function update(Request $request)
    {
              // Validationをかける
      $this->validate($request, Profile::$rules);
      // Profile Modelからデータを取得する
      $profile = Profile::find($request->id);
      // 送信されてきたフォームデータを格納する
      $profile_form = $request->all();
      unset($profile_form['_token']);
      // 該当するデータを上書きして保存する
      $profile->fill($profile_form)->save();
      
      // 課題2(php-17⑸)
      $profile_history = new ProfileHistory;
      $profile_history->profile_id = $profile->id;
      $profile_history->edited_at = Carbon::now();
      $profile_history->save();

      return redirect('admin/profile');
  }
      public function delete(Request $request)
  {
      // 該当するProfile Modelを取得
      $profile = Profile::find($request->id);
      // 削除する
      $profile->delete();
      return redirect('admin/profile/');
  }  
}
