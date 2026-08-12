<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 21:04
 */
$cmd = "bash clean.sh < input.txt > out.txt";

//$descriptorspec = array(
//    0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
//    1 => array("file", $filename, "a"), // stdout is a pipe that the child will write to
//    2 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//);
//
//$process = proc_open($command, $descriptorspec, $pipes);
//
//if (!is_resource($process)) {
//    throw new \Exception("mysqldump failed to open process: " . print_r($process, 1));
//}
//
//fclose($pipes[0]);
//
//$error = stream_get_contents($pipes[2]);
//// It is important that you close any pipes before calling
//// proc_close in order to avoid a deadlock
//$returnValue = proc_close($process);

file_put_contents('./input.txt', $_REQUEST['text']);
//var_dump($cmd);
$return = shell_exec($cmd);
$seconds = 0;
while (!$outputExists) {
    $outputExists = file_exists('./out.txt');
    sleep(1);
    $seconds++;
    if ($seconds == 30) {
        die('Timeout ERROR');
    }
}
echo json_encode(['result' => file_get_contents('./out.txt')]);
unlink('./out.txt');
