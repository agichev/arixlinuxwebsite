# Init System & Support Policy

This distribution is a fork of Artix Linux that explicitly treats **dinit** as its primary, default init system and service manager. 

Our system configurations are tailored specifically for dinit to ensure maximum performance, reliability, and simplicity.

---

## Documentation & Support Limits

While the underlying system components may technically allow the installation of alternative init systems (such as OpenRC, Runit, or S6), please be aware of our documentation policy:

* **Dinit-First Focus:** The official wiki and troubleshooting pages are written assuming you are using dinit.
* **No Guaranteed Docs for Alternatives:** We do not guarantee up-to-date or comprehensive documentation for other init systems. 
* **Community-Driven Support:** If you decide to switch your init system, you are entering "unsupported territory." You will largely need to rely on upstream Artix/Arch documentation or community-maintained guides.

---

## Why dinit?

We chose dinit because it combines the best of both worlds: the speed and dependency management of modern service managers with the lightweight, elegant, and secure design philosophy of traditional Unix init systems. 
