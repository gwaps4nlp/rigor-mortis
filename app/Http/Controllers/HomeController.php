<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use Gwaps4nlp\Repositories\UserRepository;
use Illuminate\Support\Facades\Request;
use App\Models\LogDB;
use Session;

class HomeController extends Controller
{

	/**
	 * Display the informations page.
	 *
	 * @return Illuminate\Http\Response
	 */
	public function informations()
	{
		return view('front.informations');
	}
	
	/**
	 * Display the home page.
	 *
     * @param  App\Repositories\UserRepository $user
	 * @return Illuminate\Http\Response
	 */
	public function index(UserRepository $user)
	{
		$numberUsers = $user->count();
		$numberConnectedUsers = $user->countConnected();
		$connectedUsers = $user->getConnected();
		$lastRegisteredUser = $user->getLastRegistered();
		LogDB::create([
			'session_id' => Session::getId(),
			'referer' => request()->headers->get('referer'),
			'url' => request()->fullUrl(),
		]);
		return view('front.home',compact('scores','challenge','numberUsers','numberConnectedUsers','lastRegisteredUser','connectedUsers','scores_annotations'));
	}

	/**
	 * Change language.
	 *
	 * @param  App\Jobs\ChangeLocale $changeLocale
	 * @return Illuminate\Http\Response
	 */
	public function language(
		ChangeLocale $changeLocale)
	{
		$this->dispatch($changeLocale);
		return redirect()->back();
	}

}
