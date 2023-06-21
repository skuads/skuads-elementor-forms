## Initialize Development Environment 

Run this command to from `integrations/wordpress/skuads-elementor-forms`

```bash
bin/download
```

This will download the Wordpress plugin from the subversion repository.

## Write your code

Your code will go into `integrations/wordpress/skuads-elementor-forms/skuads-elementor-forms`

Initialize your developement environment by running this command to from `integrations/wordpress/skuads-elementor-forms`

```bash
bin/bootstrap
```

## Prepare plugin for RC, PROD

Run this command to from `integrations/wordpress/skuads-elementor-forms`

```bash
bin/build {env}
```

Where {env} is `dev`, `rc`, `staging` or `prod`

This will add a compiled zip of the plugin in dist/{env}/skuads-elementor-forms.zip

You can upload this to your test wordpress installation and review the plugin.

## Release plugin to Wordpress Repo

Make sure you update the Wordpres version in `readme.txt` and `skuads-elementor-forms.php` 

Run this command to from `integrations/wordpress/skuads-elementor-forms`

```bash
bin/release
```

This will build the PROD version of the plugin, and then copy the files in to `svn/trunk`

You will then need to change directory into the svn folder. 
In this folder, you will need to run `svn status` to see the changes:

```bash
svn status
```

You can then commit and publish all changes, and they will be live

The command is something like:

```bash
svn ci -m "put your commit message here" --username skuads
```

