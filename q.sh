#!/bin/bash
echo "Commit $1"
git add -A
git commit -m "Quick Commit:$1"

git push