#!/bin/sh
# Compiles a markdown file (*.md) in `./source` into an equivalent HTML page in `./public_html`.
pandoc -s "./source/$1.md" -o "./public_html/$1.html" --template=template.html
tidy -m -i --wrap 0 --show-info no --output-html yes --tidy-mark no "./public_html/$1.html"