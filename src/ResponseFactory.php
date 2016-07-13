<?php


namespace Vice\LaravelFractal;


use Illuminate\Http\Response;

class ResponseFactory
{
    public function make($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }
}
