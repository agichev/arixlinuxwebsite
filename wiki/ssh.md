# SSH

This guide covers the installation and basic service configuration of **Pipewire** on Arix Linux. Be careful which user you run commands from.

---

## 1. Installation

Install the package using pacman.

```root #
pacman -S openssh
```

## 2. Usage

**Commands**

OpenSSH provides several commands, see each command's man page for usage information:
- [scp(1)](https://man.archlinux.org/man/scp.1.en) - secure file copy
- [sftp(1)](https://man.archlinux.org/man/sftp.1.en) - secure file transfer
- [ssh-add(1)](https://man.archlinux.org/man/ssh-add.1.en) - add private key identities to the authentication agent
- [ssh-agent(1)](https://man.archlinux.org/man/ssh-agent.1.en) - authentication agent
- [ssh-copy-id(1)](https://linux.die.net/man/1/ssh-copy-id) - use locally available keys to authorize logins on a remote machine
- [ssh-keygen(1)](https://man.archlinux.org/man/ssh-keygen.1.en) - authentication key utility
- [ssh-keyscan(1)](https://man.archlinux.org/man/ssh-keyscan.1.en) - gather SSH public keys from servers
- [sshd(8)](https://man.archlinux.org/man/sshd.8.en) - OpenSSH daemon

---

## 3. Escape sequences

During an active SSH session, pressing the tilde (~) key starts an escape sequence. Enter the following for a list of options: 

```ssh>
~?
```

---

## Passwordless authentication to a remote SSH server

Handy for git server managment.

---

**! Warning !**
Leaving the passphrase empty implies the private key file will not be encrypted. An attacker having access to the local filesystem will be able to read the private key.

---

**Note**

The default file names of the keys must not be changed, or the server may persist in asking fot a password even after running ``ssh-copy-id``. The file name will be one of:
- **id_rsa**
- **id_ecdsa**
- **id_ed25519**

depending on the key algorithm used.

---

Make sure an account for the user exists on the server. The clients' **id_ed25519.pub** will be copied to the server's **~/.ssh/authorized_keys** file in the user's home directory. 

## 4. Client

**ssh-keygen**

Clients need public and private keys. A pair may be created with ( of course, **not entering** a passphrase ):

```user $
ssh-keygen -t ed25519
```

Then authorize the public key with the server:

```user $
ssh-copy-id -i ~/.shh/id_ed25519.pub <username>@<server>```
