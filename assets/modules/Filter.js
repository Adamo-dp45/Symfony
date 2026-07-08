import { Flipper, spring } from "flip-toolkit"

/**
 * @property {HTMLElement} paginnation
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 * @property {number} page
 * @property {boolean} moreNav
 */
export default class Filter {

    /**
     * @param {HTMLElement|null} element 
     */
    constructor (element) {
        if(element === null) {
            return
        }
        this.paginnation = element.querySelector('.js-filter-paginnation')
        this.content = element.querySelector('.js-filter-content')
        this.sorting = element.querySelector('.js-filter-sorting')
        this.form = element.querySelector('.js-filter-form')

        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1)
        this.moreNav = this.page === 1 // Vaut 'true' si la page est égale à 1

        this.bindEvents()
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        this.sorting.addEventListener('click', e => {
            if(e.target.tagName === 'A') { // Si l'élément cliqué est un lien
                e.preventDefault()
                // a.getAttribute('href') -- Pour récupérer l'url du lien
                this.loadUrl(e.target.getAttribute('href'))
            }
            // aClickListener(e) -- Quand il a séparer la fonction du haut
            this.page = 1
        })

        if(this.moreNav) {
            this.paginnation.innerHTML = '<button class="btn btn-primary">Voir plus</button>'
            this.paginnation.querySelector('button').addEventListener('click', this.loadMore.bind(this))
        } else {
            this.paginnation.addEventListener('click', e => {
                if(e.target.tagName === 'A') { // Si l'élément cliqué est un lien
                    e.preventDefault()
                    this.loadUrl(e.target.getAttribute('href'))
                }
            })
        }
        // [type=checkbox] .. J'ai enlevé car je vais le faire pour tous champs
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this)) // Histoire que this face référence au bon élément
            // Avec la fonction loadForm on lui demande de traiter le formulaire, on ne vas gérer au cas par cas
        })
    }

    async loadMore() {
        const button = this.paginnation.querySelector('button')
        button.setAttribute('disabled', 'disabled')
        this.page++
        const url = new URL(window.location.href)
        const params = new URLSearchParams(url.search)
        params.set('page', this.page)
        await this.loadUrl(url.pathname + '?' + params.toString(), true)
        button.removeAttribute('disabled')
    }

    async loadForm() {
        this.page = 1 // Quand on soumet le formulaire on reset la paginnation
        const data = new FormData(this.form)
        // Si l'action du formulaire n'existe pas, je récupère l'url courante
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams() // Permet de construire les paramètes d'url dynamiquement
        data.forEach((value, key) => {
            params.append(key, value)
        })
        // Le pathname est l'url que j'ai généré dépuis mon form action ou l'url courante
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl(url, append = false) { // Le append est utilisé seulement pour la paginnation
        this.showLoader()

        const params = new URLSearchParams(url.split('?')[1] || '')
        params.set('ajax', 1) // Ajoute ajax = 1

        // const ajaxUrl = url + '&ajax=1' // Pour éviter le retour json affichage
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // On va faire un appel ajax
            }
        })
        if(response.status >= 200 && response.status < 300) {
            const data = await response.json()
            // this.content.innerHTML = data.content
            this.flipContent(data.content, append)
            this.sorting.innerHTML = data.sorting
            if(!this.moreNav) {
                this.paginnation = data.paginnation
            } else if(this.page === data.pages) {
                this.paginnation.style.display = 'none'
            } else {
                this.paginnation.style.display = 'block'
            }

            this.updatePrices(data)

            params.delete('ajax') // Avant de remplacer l'url je supprime le ajax
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
            // history.replaceState({}, '', url)
            // On remplace l'url .. On utilise replace au lieu de push car ç'est meilleur au niveau de l'expérience utilisateur en cas de retour dans le navigateur il ne reviendra pas sur les recherche précédentes mais sur la page précédente
        } else {
            console.error(response) // On peut mettre un élément d'interface pour une alerte
        }
        this.hideLoader()
    }

    /**
     * Remplace les éléments de la grille avec un effet d'animation flip
     * @param {string} content 
     */
    flipContent(content, append) {
        const springConfig = 'gentle' // Ou aussi veryGentle, noWobble, gentle, stiff, wobbly
        const exitSpring = function(element, index, onComplete) {
            spring({
                config: 'stiff', // Ou wobble -- Grafikart trouve que c'est pertinant pour la partie disparition
                values: {
                    // translateY: [-15, 0],
                    translateY: [0, -20],
                    opacity: [1, 0]
                    // opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity
                    element.style.transform = `translateY(${translateY}px)`
                },
                // delay: i * 25,
                /* onComplete: () => {
                    // add callback logic here if necessary
                } */
                onComplete
            })
        }

        const appearSpring = function(element, index) {
            spring({
                config: 'stiff', // Ou wobble -- Grafikart trouve que c'est pertinant pour la partie disparition
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity
                    element.style.transform = `translateY(${translateY}px)`
                },
                delay: index * 20, // Il ne faut pas avoir la main lourde sur le delay
            })
        }

        const flipper = new Flipper({
            element: this.content
        })
        // On parcours tous les enfants du contenu
        // this.content.children.forEach(element => {
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig, // Je l'ai rajouté pour le spring
                flipId: element.id, // L'id de mon élément
                shouldFlip: false,
                /* onExit: (element, index, remove) => {
                    window.setTimeout(() => {
                        remove()
                    }, 2000)
                } */
                onExit: exitSpring
            })
        })
        flipper.recordBeforeUpdate()
        if(append) {
            this.content.innerHTML += content
        } else {
            this.content.innerHTML = content
        }
        // Je reboucle pour recréer des flipper pourqu'il trouve la position des nouveau éléments
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig, // Pour le spring
                flipId: element.id, // L'id de mon élément
                onAppear: appearSpring
            })
        })
        flipper.update()
    }

    updatePrices({min, max}) {
        const slider = document.getElementById('price-slider')
        if(slider === null) { return }
        slider.noUiSlider.updateOptions({
            range: {
                min: [min],
                max: [max]
            }
        })
    }

    showLoader() {
        this.form.classList.add('is-loading')
        // loader = this.form.querySelector('.js-loading') -- Apparament il n'aime pas trop
        if(this.form.querySelector('.js-loading') === null) {
            return
        }
        this.form.querySelector('.js-loading').setAttribute('aria-hidden', 'false')
        this.form.querySelector('.js-loading').style.display = null
    }

    hideLoader() {
        this.form.classList.remove('is-loading')
        this.form.querySelector('.js-loading')
        if(this.form.querySelector('.js-loading') === null) {
            return
        }
        this.form.querySelector('.js-loading').setAttribute('aria-hidden', 'true')
        this.form.querySelector('.js-loading').style.display = 'none'
    }
}