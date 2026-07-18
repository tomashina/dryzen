<?php

namespace Agmedia\Api\Models;

class OC_ImpersonationEvent
{

    protected $table = DB_PREFIX . 'impersonation_event';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['query'=>'array','meta'=>'array'];
}