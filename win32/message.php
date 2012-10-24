<?php
// send when the program needs to quit
define('MESSAGE_QUIT', 1);

// create a message queue for the application
$message_queue = array();

// our default error message for cli
function message_error($message, $program) {
    include 'error.tpl.php';
    exit(1);
}

// get our messages out of the queue
function message_get($page_filter, $max_filter, $min_filter) {

    // the message queue is FIFO, so we just foreach until we find a match
    $message_queue = $GLOBALS['message_queue'];

    foreach($message_queue as $key => $message){

        // if we are matching $page_filter, check the match or continue
        if(!is_null($page_filter) && $page_filter != $message['type']) {
            continue;
        }

        // if we are matching max_filter, check the match or continue
        if($max_filter > 0 && $max_filter < $message['code']) {
            continue;
        }

        // if we are matching min_filter, check the match or continue
        if($min_filter > 0 && $in_filter > $message['code']) {
            continue;
        }

        // default is to just grab the message
        unset($GLOBALS['message_queue'][$key]);
        return $message;
    }

    // nothing in the queue
    return false;

}

// send the message where it's supposed to go
function message_dispatch($message) {
    $function_name = $message['page']->message_proc;
    return $function_name($message['page'], $message['code'], $message['data']);
}

// stick a message into the queue
function message_post($page, $id, $data = null) {
    $message = array(
        'code' => $id,
        'data' => $data,
        'page' => $page,
        'type' => $page->name,
    );

    $GLOBALS['message_queue'][] = $message;
    return true;
}

// bypass the queue and wait for the message to be dispatched immediately
function message_send($page, $id, $data = null) {
    $message = array(
        'code' => $id,
        'data' => $data,
        'page' => $page,
        'type' => $page->name,
    );

    return message_dispatch($message);
}

// quit our application
function message_quit() {
    exit(0);
}