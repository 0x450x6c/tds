{ nixpkgs ? import <nixpkgs> {} }:
with nixpkgs;
mkShell {
	nativeBuildInputs = [
		php74 git fish
		nodejs-13_x yarn
	];

	php_xdebug = php74Packages.xdebug;
	php_composer = php74Packages.composer;
	php_ast = php74Packages.ast;

	projectRoot = builtins.toString ./.;

	inherit php74;

	shellHook = ''
		export PATH="$projectRoot/tools/bin:$PATH:$projectRoot/vendor/bin"

		find $projectRoot/tools/etc \
		\( -name '*.template' -or -name '*.template.*' \) -exec \
		bash -c 'file={}; envsubst < $file > "''${file/.template/}"' \;
	'';
}
