<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PCI - PHP Continuous Integration Tool</title>
  <link rel="stylesheet" href="/assets/style.css">
  <script src="/assets/jquery-3.2.1.min.js"></script>
  <script src="/assets/script.js"></script>
</head>
<body>
  <?php include 'header.php';?>  
  
  <table>
    <tbody>
      <tr>
        <td><span><?= $owner.'/'.$project; ?></span> <img src="/assets/passing.svg" /> <img src="/assets/reload.png" /></td>
        <td>master: Update README.md</td>
        <td>#<?= $build;?> passed</td>
        <td>Execution time: 29 sec</td>
        <td>6 days ago</td>
      </tr>
      <tr>
        <td valign="top">
          <ul class="list-group">
            <li class="list-group-item passed">master <a href="/projects/<?= $owner.'/'.$project; ?>/builds/5">#5</a></li>
            <li class="list-group-item failed">v1.0.1 <a href="/projects/<?= $owner.'/'.$project; ?>/builds/4">#4</a></li>
            <li class="list-group-item failed">master <a href="/projects/<?= $owner.'/'.$project; ?>/builds/3">#3</a></li>
            <li class="list-group-item passed">develop <a href="/projects/<?= $owner.'/'.$project; ?>/builds/2">#2</a></li>
            <li class="list-group-item passed">poc <a href="/projects/<?= $owner.'/'.$project; ?>/builds/1">#1</a></li>
          </ul>
        </td>
        <td colspan="4" class="console">
        <pre class="i-has-teh-code">Buildfile: /home/server/www/pci/workspace/1c844bac2dbb5814242be1cb1a45594e42180718/build.xml

TOKEN > prepare:

     [echo] Making directory ./build
    [mkdir] Created dir: /home/server/www/pci/workspace/1c844bac2dbb5814242be1cb1a45594e42180718/build

TOKEN > build:

..... 5 / 5 (100%)


Time: 201ms; Memory: 6Mb

PHPUnit 6.5.5 by Sebastian Bergmann and contributors.

....                                                                4 / 4 (100%)

Time: 298 ms, Memory: 6.00MB

OK (4 tests, 4 assertions)

Generating code coverage report in Clover XML format ... done

Generating code coverage report in HTML format ... done

Generating code coverage report in PHP format ... done


[1;37;40mCode Coverage Report:     [0m
[1;37;40m  2018-01-09 21:44:29     [0m
[1;37;40m                          [0m
[1;37;40m Summary:                 [0m
[30;42m  Classes: 100.00% (4/4)  [0m
[30;42m  Methods: 100.00% (6/6)  [0m
[30;42m  Lines:   100.00% (14/14)[0m

\Command::Command\RandomInteger
  [30;42mMethods: 100.00% ( 2/ 2)[0m   [30;42mLines: 100.00% (  9/  9)[0m
\Middleware::Middleware\XContentTypeOptions
  [30;42mMethods: 100.00% ( 1/ 1)[0m   [30;42mLines: 100.00% (  1/  1)[0m
\Middleware::Middleware\XssProtection
  [30;42mMethods: 100.00% ( 1/ 1)[0m   [30;42mLines: 100.00% (  1/  1)[0m
\Service::Service\Crypto
  [30;42mMethods: 100.00% ( 2/ 2)[0m   [30;42mLines: 100.00% (  3/  3)[0m

BUILD FINISHED

Total time: 0.7801 seconds</pre>
        </td>
      </tr>
    </tbody>
  </table>
</body>
</html>
