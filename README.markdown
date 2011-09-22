# External script/CSS loading library for CodeIgniter

Simple library to manage/schedule the loading of script and CSS files from your
controllers.

Example Usage:

Suppose relative to the top level index.php that your scripts exist in a
directory called 'assets/scripts/', and CSS in 'assets/style'.

Suppose your stylesheet is called 'style.css', and you want to load a copy of
jQuery.

In your controller:

```php
<?php

$this->load->library('scripts');
// sets the path
$this->scripts->set_dir('css', 'assets/style');
$this->scripts->set_dir('js', 'assets/scripts');

$this->scripts->js('jquery-1.6'); // this is globbed against *jquery-1.6*.js
$this->scripts->css('style.css', true); // the second argument tells us that it
                // is an exact file pattern, i.e. no globbing
```


Then in your view:

```php
<head>
<?= $this->scripts->html(); ?>
</head>
```

Note that the html function can take a single argument which if set to TRUE will
output the contents of the included files, instead of using src/href attributes.

## TODO

  + It can't really handle subdirs unless the full path is given - globbing should be recursive