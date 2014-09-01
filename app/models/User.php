<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait, SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function groups() {
		return $this->belongsToMany('Group', 'groupings');
	}

	public function fbAccount() {
		return $this->hasOne('FacebookAccount');
	}

	public function profileImage() {
		return $this->hasOne('ProfileImage');
	}

	public function snaps() {
		return $this->hasMany('StreetSnap', 'user_id');
	}

}
