<?php
error_reporting(-1);

// we need our message library and our page library
include 'message.php';
include 'page.php';

// our "winproc" equivalent - the dispatcher to handle all messages to our test page
function test_page_proc($page, $id, $data = null) {

    // we will switch on our message id
    switch($id) {
        case MESSAGE_QUIT:
            message_quit(0);
            break;
        case PAGE_ARG_PARSE:
            if ($_SERVER['argc'] > 1) {
                $temp = $_SERVER['argv'];
                unset($temp[0]);
                return implode(' ', $temp);
            }
            break;
        default:
            return page_default_proc($page, $id, $data);
    }

    return 0;
}

// our "winmain" equivalent, we're doing pages instead of windows
function main($program_name) {

    // step 1: register our page type
    $page_type = array(
        'message_proc' => 'test_page_proc',
        'program' => $program_name,
        'style' => 'test.css',
        'logo' => 'test.png',
        'layout' => 'test.tpl.php',
        'name' => 'test',
        );

    if (!page_register_type($page_type)) {
        message_error('Page Registration Failed', $program_name);
        return 0;
    }

    // step 2: create our page and display it
    $page = page_create(
                        'test',
                        'My test page',
                        $program_name);

    if (!$page) {
        message_error('Page Creation Failed', $program_name);
        return 0;
    }

    page_display($page);

    // step 3: loop our message queue until we're done with the page
    while($message = message_get(null, 0, 0)) {
        message_dispatch($message);
    }

    return $message['code'];
}

// start our program
main('test_program');