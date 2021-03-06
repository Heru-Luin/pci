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
        <td><span><?= $owner.'/'.$project; ?></span> <img src="/assets/<?php $status = 'passed'; if ((int) $build['status'] === 2) $status = 'failed'; echo $status;  ?>.svg" />
        <td><?= $build['payload']['ref'];?>: <?= $build['payload']['head_commit']['message'];?></td>
        <td>#<?= $build['sha'];?> passed</td>
        <td>Execution time: <?= $build['execution_time'];?> sec</td>
        <td><?=$build['created_at'];?></td>
      </tr>
      <tr>
        <td valign="top">
          <input type="text" id="search-build" class=".input" placeholder="Search builds ..." autofocus/>
          <ul class="list-group" id="builds">            
            <?php foreach($history as $row){?>
              <li class="list-group-item <?php $status = 'passed'; if ((int) $row['status'] === 2) $status = 'failed'; echo $status;  ?>">master <a href="/projects/<?= $owner.'/'.$project; ?>/builds/<?=$row['sha'];?>">#<?=$row['sha'];?></a></li>
            <?php } ?>
          </ul>
        </td>
        <td colspan="4" class="console">
        <pre class="i-has-teh-code"><?=$build['output'];?></pre>
        </td>
      </tr>
    </tbody>
  </table>
</body>
</html>
