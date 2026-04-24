{
  pkgs ? import <nixpkgs> {
    config.allowUnfree = true;
  },
}:
pkgs.mkShell {
  buildInputs = with pkgs; [
    nodejs
    php
    php.packages.composer
    laravel
    nodePackages.intelephense
    vtsls
    vscode-langservers-extracted
    vue-language-server
  ];
}
