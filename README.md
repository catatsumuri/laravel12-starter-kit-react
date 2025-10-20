```
docker run --rm -it -v "$PWD":/app -w /app composer \
  create-project laravel/react-starter-kit . --stability=dev
```

## how to merge upstream 

```
git remote add upstream https://github.com/laravel/react-starter-kit.git
git fetch upstream

git diff --name-status upstream/main -- ':!vendor' ':!node_modules'

git checkout upstream/main -- resources/js/... app/... routes/... config/...
git add -A
git commit -m "chore: port changes from upstream/main (2FA UI etc.)"
```
