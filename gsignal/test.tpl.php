-----------------------------------------------------------------
                Welcome to our test!
-----------------------------------------------------------------
    This is a boring test program

<?php if(isset($argument)):?>
    You sent <?php echo $argument?> as an argument
<?php else:?>
    You did not send any arguments via the command line
<?php endif?>
-----------------------------------------------------------------
<?php if(isset($bork)) {echo $bork;}?>