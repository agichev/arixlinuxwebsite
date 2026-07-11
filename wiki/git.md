# git

**Git** is a widely used, open source, distributed version control system, mainly written in C.

Git was created by Linus Torvalds for use developing the Linux kernel and other open source projects, with the stated goals of being distributed, fast, and to guarantee that output exactly matches input. It was first released in 2005, and has since become the most widely used distributed version control system. 

This article will cover getting started with Git, and general usage. 

---

## 1. Installation

Install the git package using pacman

```root #
pacman -S git
```

---

## 2. Configuration

In order to use Git you need to set at least a name and email: 

```user $
git config --global user.name "Mikola"
git config --global user.email "Mikola@ExampleMail.com"
```

---

**Note**

To prevent email spam, it is possible to set a blank email address as follows:
```user $
git config --global user.email ""
```

---

## 3. Local

If there will be just one user of the project, or when creating something which will be shared in a distributed way, start on the local workstation.

If the intent is to have a central server which everyone uses as the "official" server (e.g. GitHub) then it might be easier to create an empty repository there.

The next list of commands will describe how to create a repository on a workstation: 

```user $
cd ~/src
```

```user $
mkdir hello
```

```user $
cd hello
```

```user $
touch readme.txt
```

```user $
git init
```

The local repository has now been created. 

---

**Note**

The actual repository resides the ``.git`` folder, so don't delete it or the parent ``hello`` folder, which would mean losing everything.

---

Let's make some edits: 

```user $
echo "Example" > readme.txt
```

The new ``readme.txt`` file must be added ( staged ) before it can be included in the git repository. Use the next commands to stage the file and to make the commit: 

```user $
git add readme.txt
```

```user $
git commit -m "Added text to readme.txt"
```

One of many features of git - on commit message writing screen (fot example in Vim) [you can see the diff](https://stackoverflow.com/questions/4750148/git-show-index-diff-in-commit-message-as-comment/46160765#46160765)

**~/.gitconfig**
``
[commit]
    verbose = true
``
> ` FILE ` **`/etc/portage/make.conf`** **Example *MAKEOPTS* declaration**
> 
> `# If left undefined, Portage's default behavior is to:`
> `# - set the MAKEOPTS jobs value to the same number of threads returned by 'nproc'`
> `# - set the MAKEOPTS load-average value slightly above the number of threads returned by 'nproc'...`
> `# Please replace '4' as appropriate for the system (min(RAM/2GB, threads), or leave it unset.`
> `MAKEOPTS="-j4 -l15"`
---

## 4. Common commands

Clone a repository:

```user $
git clone git@example.com:/repository.git
```

```user $
git lone git://example.com:/repository.git
```
