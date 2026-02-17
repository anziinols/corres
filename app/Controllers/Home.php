<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('landing_page');
    }

    public function about(): string
    {
        return view('about');
    }

    public function contact(): string
    {
        return view('contact');
    }

    public function contactSubmit()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'subject' => 'required',
            'message' => 'required|min_length[10]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check your input and try again.');
        }

        // In a real application, you would send an email here
        // For now, we'll just show a success message
        return redirect()->back()
            ->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
