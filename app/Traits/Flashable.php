<?php

namespace App\Traits;

use App\Mail\MailSender;

trait Flashable
{

    /**
     * @param $template
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function flash($template)
    {
        session(['flash' => view('partial.' . $template)->render()]);
        return redirect('/flash');
    }
}