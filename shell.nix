{ nixpkgs ? import <nixpkgs> {} }:
with nixpkgs;
mkShell {
	nativeBuildInputs = [
		php74 git fish
		nodejs-13_x yarn
	];

	php_composer = php74Packages.composer;
	php_ast = php74Extensions.ast;
	php_xdebug = php74Extensions.xdebug;
	php_readline = php74Extensions.readline;
	php_json = php74Extensions.json;
	php_dom = php74Extensions.dom;
	php_simplexml = php74Extensions.simplexml;
	php_filter = php74Extensions.filter;
	php_iconv = php74Extensions.iconv;
	php_openssl = php74Extensions.openssl;
	php_tokenizer = php74Extensions.tokenizer;
	php_mbstring = php74Extensions.mbstring;
	php_xmlwriter = php74Extensions.xmlwriter;

	projectRoot = builtins.toString ./.;

	php = php74.unwrapped;

	shellHook = ''
		export PATH="$projectRoot/tools/bin:$PATH:$projectRoot/vendor/bin"

		find $projectRoot/tools/etc \
		\( -name '*.template' -or -name '*.template.*' \) -exec \
		bash -c 'file={}; envsubst < $file > "''${file/.template/}"' \;
	'';
}
