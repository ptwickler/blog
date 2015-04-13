<?php
$n = 1;
while ($n < 100) {
if ($n%3 ==0 && $n %5 == 0){
        echo "FizzBuzz<br/>";
    }
   elseif  ($n % 3 ==0){
        echo "Fizz<br/>";
    }

    elseif($n%5==0){
        echo "Buzz<br/>";
    }



    else {
        echo $n . "<br/>";
    }
    $n++;

}