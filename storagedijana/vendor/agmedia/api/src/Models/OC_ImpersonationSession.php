<?php

namespace Agmedia\Api\Models;

class OC_ImpersonationSession
{

    protected $table = DB_PREFIX . 'impersonation_session';
    public $timestamps = false;
    protected $guarded = [];
    public function events() {
        return $this->hasMany(ImpersonationEvent::class, 'impersonation_session_id');
    }
}