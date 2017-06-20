# .editorconfig templates and generator

This is a collection of `.editorconfig` templates.


## Generator

The generator is a script to merge root config file and some config files for specific file type.

```
root = true

[*]
trim_trailing_whitespace = true
insert_final_newline = true
end_of_line = lf
indent_size = 4

[*.html]
indent_style = space
indent_size = 2

[*.{css,sass,scss}]
indent_style = space
indent_size = 2

[*.{js,ts}]
indent_style = space
indent_size = 2

[*.json]
indent_style = space
indent_size = 2

[*.{yml,yaml}]
indent_style = space
indent_size = 2
```

For example, this is for web development includes `root + html + css + javascript + files for config`.

http://kanonji.info/editorconfig?key=web

The generator is running here above.

http://kanonji.info/editorconfig?key=web&download=1

Download it as `.editorconfig` file name.

```
$ curl -LOJ "http://kanonji.info/editorconfig?key=web&download=1"
```

The key can be file name before `.editorconfig` in this repos or defined as `$configFileMap` in https://github.com/kanonji/editorconfig/blob/master/index.php
