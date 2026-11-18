import './stimulus_bootstrap.js'; // Anciennement './bootstrap.js' (renommé par la recette symfony/stimulus-bundle)
import './css/app.scss'
// ----- //
import Button from "bootstrap/js/src/button"
import Collapse from "bootstrap/js/src/collapse"
import Alert from 'bootstrap/js/src/alert'
import Carousel from 'bootstrap/js/src/carousel'
import Dropdown from 'bootstrap/js/src/dropdown'
import Modal from 'bootstrap/js/src/modal'
import Offcanvas from 'bootstrap/js/src/offcanvas'
import Tab from 'bootstrap/js/src/tab'
// ----- //
import $ from 'jquery'
import "slick-carousel"
import "slick-carousel/slick/slick.css"
import "slick-carousel/slick/slick-theme.css"
import select2 from 'select2'
import "select2/dist/css/select2.css"
import Filter from "./modules/Filter"
import './modules/Captcha.js'
import noUiSlider from 'nouislider'
import 'nouislider/dist/nouislider.css'
// ----- //
import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { createApp } from 'vue'
import Compteur from './components/Compteur.vue'
import App from './components/Compteur.jsx'
import './components/Comment.jsx'
// ----- //
import TomSelect from 'tom-select'
import 'tom-select/dist/css/tom-select.bootstrap5.min.css'
import EasyMDE from "easymde"
import "easymde/dist/easymde.min.css"
// ----- //
import Places from 'places.js'
// ----- //
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-control-geocoder'
import 'leaflet-control-geocoder/dist/Control.Geocoder.css'
// Fix des icônes Leaflet
import iconUrl from 'leaflet/dist/images/marker-icon.png'
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png'
import shadowUrl from 'leaflet/dist/images/marker-shadow.png'
// ----- //
import $script from 'scriptjs'
import confetti from 'canvas-confetti'
import 'intro.js/introjs.css'
import introJs from 'intro.js'

document.addEventListener('turbo:load', () => {
    new Filter(document.querySelector('.js-filter'))
    const slider = document.getElementById('price-slider')
    if(slider) {
        const min = document.getElementById('min')
        const max = document.getElementById('max')
        const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10 // Arrondi à la virgule inférieur
        const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10 // Virgule superieur
        const range = noUiSlider.create(slider, {
            // start: [20, 80], -- Par défaut
            start: [min.value || parseInt(slider.dataset.min, 10), max.value || parseInt(slider.dataset.max, 10)],
            connect: true,
            step: 10, // On ne veut pas les nombres a virgules : de 10 en 10
            range: {
                'min': parseInt(slider.dataset.min, 10),
                'max': parseInt(slider.dataset.max, 10)
            }
        })
        range.on('slide', function(values, handle) {
            if(handle === 0) {
                min.value = Math.round(values[0])
            }
            if(handle === 1) {
                max.value = Math.round(values[1])
            }
        })
        range.on('end', function(value, handle) { // Au moment ou tu as fini de changer la valeur des champs
            min.dispatchEvent(new Event('change')) // Ajoute un évènement de type change sur un des champs
        })
    }
    // ----- //
    $('.select').select2() // Destroy choices dans js à cause du retour
    // -- Carousel -- //
    $('[data-slider]').slick({
        dots: true, // Affiche des points de navigation
        arrows: true, // Active les flèches de navigation
        autoplay: true, // Démarre automatiquement
        autoplaySpeed: 2000, // Change d'image toutes les 2 secondes
        infinite: true, // Boucle infinie
        speed: 500, // Temps de transition
        slidesToShow: 1, // Nombre d'images visibles
        slidesToScroll: 1, // Nombre d'images défilées à chaque clic
        responsive: [ // Configuration pour différentes tailles d'écran
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                }
            }
        ]
    })
    // -- Boutons personalisés
    $('#slick-prev').on('click', function () {
        $('.slider').slick('slickPrev')
    })

    $('#slick-next').on('click', function () {
        $('.slider').slick('slickNext')
    })
    // ----- //
    new EasyMDE({ // el
        element: document.querySelector('[data-easy]'),
        toolbar: ["bold", "italic", "heading", "code", "quote", "strikethrough", "undo", "|", "link", "image", "|", "preview", "side-by-side", "fullscreen"],
        spellChecker: false,  // Désactiver le correcteur orthographique si non nécessaire
        placeholder: "Rédigez votre texte en Markdown...",
    })
    // ----- //
    const contactForm = document.querySelector('#contactForm')
    const form = document.querySelector('#form')
    if(contactForm && form) {
        contactForm.addEventListener('click', e => {
            e.preventDefault()
            form.style.display = "initial"
            contactForm.style.display = "none"
        })
    }
    // -- FormType -- //
    async function jsonFetch(url) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json'
            }
        })
        if(response.status === 204) {
            return null
        }
        const data = await response.json()
        if(response.ok) {
            return data
        }
        throw response
    }

    /**
     * @param {HTMLSelectElement} select
     */
    function bindSelect(select) {
        new TomSelect(select, {
            hideSelected: true, // Permet de supprimer les options qui sont déjà selectionnées de la recherche
            closeAfterSelect: true, // Quand on selectionne une valeur on l'a cache automatiquement
            plugins : {
                remove_button : {title: 'Supprimer'}
            },
            valueField: select.dataset.value, // Dans la reponse voici le champ que tu dois utilisé - 'id'
            labelField: select.dataset.label, // Champ utilisé pour le label - 'name'
            searchField: select.dataset.label, // Champ utiliser pour la recherche - 'name'
            load: async(query, callback) => { // Permettra de faire la recherche
                const url = `${select.dataset.remote}?q=${encodeURIComponent(query)}`
                callback(await jsonFetch(url))
            }
        })
    }
    Array.from(document.querySelectorAll('select[multiple]')).map(bindSelect)
    // -- Leaflet - OpenStreetMap -- //
    L.Icon.Default.mergeOptions({ iconUrl, iconRetinaUrl, shadowUrl })
    let $map = document.querySelector('#map')

    class LeafletMap {

        constructor() {
            this.map = null
            this.bounds = [] // Va me permettre d'ajouter plusieurs points
        }

        /**
         * 
         * @param {HTMLElement} element 
         */
        async load(element) { // async est utile ici si j'utilise script js
            return new Promise((resolve, reject) => {
                this.map = L.map(element, {
                    // center: [], -- Si on veut définir un centre par défaut
                    // layers: [], -- Couche, on pourra choisir des tuiles en fonction ou afficher ou masquer des choses
                    // scrollWheelZoom: false, -- Permet de désactiver le zoom lorsqu'on scroll sur la carte
                })// .setView([5.345317, -4.024429], 13) -- On ne vas pas utiliser le setView car on vas centrer la carte par rapport au différents biens qu'on a
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { // 'tileLayer' indique la manière de charger la carte
                    attribution: `&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors`, // Pour la mention légal, dans ce cas on aura pas besoin de l'id et de l'accès token
                    /* -- Mapbox - Si .. on doit utiliser son 'tileLayer('//api.tiles.mapbox.com..)' et son 'attribution' aussi
                        id: 'mapbox.streets', -- Le type de carte qu'on veut utiliser
                        accessToken: '', -- La clé d'api que mapbox nous donne
                    */
                    minZoom: 2, // Plus le zoom sera petit la carte sera éloigné
                    maxZoom: 30,
                }).addTo(this.map)
                /* -- Si on veut ajouter un autre fournisseur de carte plutôt que celui du 'tileLayer'
                    this.map.addLayer(new L.StamenTileLayer('watercolor', {
                        detectRetina: true -- Permet de changer la couleur
                    }))
                */
                resolve()
            })
        }

        addMarker(lat, lng, text) {
            let point = [lat, lng]
            this.bounds.push(point)
            return new LeafletMarket(point, text, this.map)
        }

        /**
         * Permet de centrer la carte
         */
        center() {
            this.map.fitBounds(this.bounds)
        }
    }

    class LeafletMarket {

        constructor(point, text, map) {
            this.text = text
            this.popup = L.popup({
                autoClose: false, // On ne veut pas fermer cet élément
                closeOnEscapeKey: false,
                closeOnClick: false,
                closeButton: false, // -- On ne veut pas le fermer
                className: 'marker', // Nom de class si on veut le stylisé
                maxWidth: 400
            })
            .setLatLng(point) // Point centrale de la carte
            .setContent(text) // On peut y mettre du html grâce au poppup
            .openOn(map) // On l'ouvre sur cette carte
        }

        setActive() { // On vas lui rajouter la class is-active à l'élément s'il est actif
            this.popup.getElement().classList.add('is-active')
        }

        unsetActive() { // On vas lui enlever la class is-active à l'élément si on le quitte le survol
            this.popup.getElement().classList.remove('is-active')
        }

        addEventListener(event, cb) {
            this.popup.addEventListener('add', () => { // () => : Car on veut qu'il fasse référence à notre class
                this.popup.getElement().addEventListener(event, cb)
            })
        }

        setContent(text) {
            this.popup.setContent(text)
            this.popup.getElement().classList.add('is-expanded')
            this.popup.update() // Permet à la popup d'automatiquement recalculer la largeur et d'être positionner correctement
        }

        resetContent() {
            this.popup.setContent(this.text)
            this.popup.getElement().classList.remove('is-expanded')
            this.popup.update()
        }
    }

    const initMap = async function() {
        let map = new LeafletMap()
        let hoverMarker = null
        let activeMarker = null
        await map.load($map)
        Array.from(document.querySelectorAll('.js-marker')).forEach((item) => { // Attention si on vise des navigateurs qui ne supporte pas Array.from, aller sur Mdn pour récupérer le polyfil
            let marker = map.addMarker(item.dataset.lat, item.dataset.lng, item.dataset.price + ' FCFA')

            item.addEventListener('mouseover', function() { // Lorsqu'on passe sur l'élément
                if(hoverMarker !== null) {
                    hoverMarker.unsetActive()
                }
                marker.setActive()
                hoverMarker = marker
            })
            item.addEventListener('mouseleave', function() { // Lorsqu'on quitte le survol de l'élément
                if(hoverMarker !== null) {
                    hoverMarker.unsetActive()
                }
            })
            marker.addEventListener('click', function() { // Lorsqu'on clique sur le marker
                if(activeMarker !== null) {
                    activeMarker.resetContent()
                }
                marker.setContent(`<div class="js-laflet-title">${item.dataset.title}</div>
                    `) // item.innerHTML
                activeMarker = marker
            })
        }) 
        map.center()
    }
    if($map !== null) {
        initMap()
    }
    // ----- //
    let $mapShow = document.querySelector('#mapShow')
    if($mapShow !== null) {
        let lat = $mapShow.dataset.lat ?? null
        let lng = $mapShow.dataset.lng ?? null
        if(lat !== null && lng !== null) {
            let mapS = L.map($mapShow, {
                // center: [], -- Ou utiliser le setView
                // zoom: 13, -- Ou dans le setView
                // dragging: false, -- Empêcher de deplacer la carte
                // zoomControl: false, // Masque les boutons de zoom
                // scrollWheelZoom: false, -- Empêche le zoom avec la molette
                // doubleClickZoom: false, -- Empêche le zoom avec le double clique
                // boxZoom: false,  -- Empêche le zoom par selection
                // keyboard: false, -- Empêche le contrôle au clavier
            }).setView([lat, lng], 13)

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                minZoom: 12,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' // On doit mettre la bonne attribution qui apparaît en bas de la carte pour être en accord avec les conditions d'utilisation de la carte qu'on n'a choisi
            }).addTo(mapS)

            /* - Pour choisir une icone personalisée
                let icon = L.icon({
                    iconUrl: '/images/icon.png',

                    shadowUrl: '/images/icon-shadow.png' -- Si on veut un effet d'ombre
                    iconSize: [38, 95]
                    Pour les autres options voir la documentation
                })
            */

            L.marker([lat, lng],{
                // icon: icon -- !!
            }).addTo(mapS)
                .bindPopup($mapShow.dataset.text)
                .openPopup()
        } else {
            $mapShow.innerHTML = `<p>Coordonnées non définies pour ce bien</p>`
        }
    }
    // -- Algolia places -- //
    /*
    let inputAddress = document.querySelector('#property_form_address')
    if(inputAddress !== null) {
        let place = Places({
            container: inputAddress, // L'élément qui sera remplacé par le système d'autocomplétion
        })

        place.on('change', e => {
            document.querySelector('#property_form_city').value = e.suggestion.city
            document.querySelector('#property_form_postal_code').value = e.suggestion.postcode
            document.querySelector('#property_form_latitude').value = e.suggestion.latlng.lat
            document.querySelector('#property_form_longitude').value = e.suggestion.latlng.lng
        })
    }
    // --
    let searchAddress = document.querySelector('.search_address') // '#search_address'
    if(searchAddress !== null) {
        let place = Places({
            container: searchAddress, // L'élément qui sera remplacé par le système d'autocomplétion
        })

        place.on('change', e => {
            document.querySelector('#lat').value = e.suggestion.latlng.lat
            document.querySelector('#lng').value = e.suggestion.latlng.lng
        })
    }
    */
    // -- IntroJs -- //
    introJs.tour().start()
})
// --- Suppression des images de propriétés --- //
document.querySelectorAll('[data-delete]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault()
        fetch(a.getAttribute('href'), {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({'_token': a.dataset.token})
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                a.parentNode.parentNode.removeChild(a.parentNode)
            } else {
                alert(data.error)
            }
        })
        .catch(e => alert(e))
    })
})
// ----- //
let vue = document.querySelector('#vue')
if(vue !== null) {
    createApp(Compteur).mount(vue)
}
// ----- //
const app = document.querySelector('#react')
if(app) {
    createRoot(app).render(
        <StrictMode>
            <App />
        </StrictMode>
    )
}
// --- Google maps --- //
/*
    let $map = document.querySelector('#map')

    class GoogleMap {

        constructor() {
            this.map = null
            this.bounds = null
            this.textMarker = null
        }

        /**
         * Charge la carte sur un élément
         * @param {HTMLElement} element 
         *
        async load(element) { // async : Car la fonction va contenir des traitements asynchrones, le $script va charger google maps et va ensuite initialiser la carte, donc on dit ici que la fonction retournera une promesse

            // await google.maps.importLibrary("maps") -- Ou dans $script
            // await google.maps.importLibrary("marker")

            return new Promise((resolve, reject) => {
                $script('https://maps.googleapis.com/maps/api/js?key=596826342838-cinfi270njavvu9sc16d19fri99cui1k.apps.googleusercontent.com&libraries=maps', () => { // Ici on utilise une fonction flèché pour que this.map face référence à notre objet

                    this.textMarker = class TextMarker extends google.maps.OverlayView { // Pour personaliser le marker de google

                        constructor (pos, map, text) {
                            super()
                            this.div = null
                            this.pos = pos
                            this.text = text
                            this.setMap(map)

                            this.html = null
                            this.enableCallbacks = []
                        }

                        /**
                         * Appeler lorsqu'un élément est ajouter à la carte
                         *
                        onAdd () {
                            this.div = document.createElement('div')
                            this.div.classList.add('marker')
                            this.div.style.position = 'absolute'
                            this.div.innerHTML = this.text
                            this.getPanes().overlayImage.appendChild(this.div)

                            this.div.addEventListener('click', (e) => {
                                e.preventDefault()
                                e.stopPropagation()
                                this.activate()
                            })
                        }

                        draw () {
                            let position = this.getProjection().fromLatLngToDivPixel(this.pos)
                            this.div.style.left = position.x + "px"
                            this.div.style.top = position.y + "px"
                        }

                        /**
                         * Permet de supprimer l'élément lorsque l'overlay est supprimé
                         *
                        onRemove () {
                            this.div.parentNode.removeChild(this.div)
                        }

                        hover() {
                            if(this.div !== null) {
                                this.div.classList.add('is-active')
                            }
                        }

                        out() {
                            if(this.div !== null) {
                                this.div.classList.remove('is-active')
                            }
                        }

                        activate() {
                            if(this.div !== null) {
                                this.div.innerHTML = this.html
                            }
                            this.enableCallbacks.forEach(function (cb) { cb() }) // On l'es appelle les uns à la suite des autres
                        }

                        deactivate() {
                            if(this.div !== null) {
                                this.div.innerHTML = this.text
                            }
                        }

                        setContent(html) {
                            this.html = html
                        }
                    }

                    let center =  { lat: -34.397, lng: 150.644 }
                    this.map = new google.maps.Map(element/* , { -- On peut se passer des options car on va centrer la carte en fonction des markers
                        center: center,
                        zoom: 8,
                        // mapId: "DEMO_MAP_ID",
                    }*)
                    this.bounds = new google.maps.LatLngBounds() // Permet de centrer la carte en fonction des markers
                    resolve()
                }) // key=API_KEY
            })
        }

        /**
         * Ajouter un marker sur la carte
         * @param {string} lat 
         * @param {string} lng 
         * @param {string} text 
         * @returns {TextMarker}
         *
        addMarker(lat, lng, text) {
            let center =  { lat: -34.397, lng: 150.644 }
            let point = new google.maps.LatLng(parseFloat(lat), parseFloat(lng)) // Ou 'center'

            let marker = this.textMarker(point, this.map, text) // Utiliser le marker personalisé
            marker.enableCallbacks.push(() => {
                this.map.setCenter(marker.pos)
            })

            // const marker = new google.maps.marker.AdvancedMarkerElement({
            //     title: 'ss',
            //    map: this.map,
            //   position: point, 
            // })
            this.bounds.extend(point) // Permet d'étendre les limites de la carte en fonction des markers, extend avec le nouveau point

            return marker
        }

        /**
         * Centre la carte pour englober les markers
         *
        centerMap() {
            this.map.panToBounds(this.bounds) // Centre la carte en fonction des markers
            this.map.fitBounds(this.bounds) // Zoom et centre la carte en fonction des markers
        }
    }

    class MapBoxMap { // On peut utiliser aussi mapbox
    }

    const initMap = async () => {
        let activeMarker = null
        let enableMarker =  null
        let map = new GoogleMap()
        await map.load($map)
        Array.from(document.querySelectorAll('.js-marker')).forEach(item => {
            let marker = map.addMarker(item.dataset.lat, item.dataset.lng, item.dataset.price + ' FCFA')
            marker.setContent(item.innerHTML)
            marker.enableCallbacks.push(function () {
                if(enableMarker !== null) {
                    enableMarker.deactivate()
                }
                enableMarker = marker
            })
            item.addEventListener('mouseover', () => {
                marker.hover()
                if(activeMarker !== null) {
                    marker.out()
                }
                activeMarker = marker
            })
            item.addEventListener('mouseleave', () => {
                if(activeMarker === marker) {
                    marker.out()
                    activeMarker = null
                }
            })
        })
        map.centerMap()
    }

    if($map !== null) {
        initMap() // Ou..
        // let map = new GoogleMap()
        // map.load($map).then(() => {
        //    Array.from(document.querySelectorAll('.js-marker')).forEach(item => {
        //        map.addMarker(item.dataset.lat, item.dataset.lng, item.dataset.price + ' FCFA')
        //    })
        // })
    }
*/

// ----- //
let confet = document.querySelector('.confetti')
if(confet !== null) {
    confet.addEventListener('click', () => {
        confetti({
            particleCount: 200,
            spread: 100,
            origin: { y: 0.6 }
        })
    })
}
// --- Turbo --- //
/*
document.addEventListener('turbo:submit-start', (event) => { // Lors de la soumission du formulaire
    const form = event.target
    const button = form.querySelector('button[type="submit"]')
    if (!button) return
    button.querySelector('.js-btn-text').classList.add('hidden')
    button.querySelector('.js-btn-spinner').classList.remove('hidden')
})

document.addEventListener('turbo:submit-end', (event) => { // Quand la requête est terminée, Tu remets l’état normal (turbo:submit-end)
    const form = event.target
    const button = form.querySelector('button[type="submit"]')
    if (!button) return
    button.querySelector('.js-btn-text').classList.remove('hidden')
    button.querySelector('.js-btn-spinner').classList.add('hidden')
})
*/
document.addEventListener("turbo:load", () => {
    window.addEventListener("hashchange", () => {
        const hash = window.location.hash
        if (hash) {
            const target = document.querySelector(hash)
            if (target) {
                target.scrollIntoView({ behavior: "smooth" }) // Scroll vers l'ancre
                // turbo.visit(window.location.href) -- OU déclencher une navigation Turbo si tu veux charger une ressource
            }
        }
    })
})
// --- Recherche globale --- //
document.addEventListener('turbo:load', () => {
    const input = document.getElementById('search-input')
    const results = document.getElementById('search-results')
    let timeout = null;

    if(input) {
        input.addEventListener('input', () => {
            clearTimeout(timeout)

            const query = input.value.trim()
            if (query.length < 2) {
                results.innerHTML = ''
                return;
            }

            timeout = setTimeout(() => {
                // Performance
                const cacheKey = `search_${query.toLowerCase()}`;
                const cached = localStorage.getItem(cacheKey);

                if (cached) {
                    const data = JSON.parse(cached);
                    displayResults(data);
                } else {
                    fetch(`/fr/api/search?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Performance
                        data.query = query; // on stocke le terme recherché pour le highlight
                        localStorage.setItem(cacheKey, JSON.stringify(data));

                        displayResults(data);
                    })
                    .catch(error => {
                        console.error('Erreur de recherche :', error);
                    });
                }
            }, 300); // délai pour éviter les requêtes trop fréquentes
        });
    }

    function displayResults(data) {
        const { category, users, query } = data;
        let html = '';

        // H
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // échappe les caractères spéciaux
        const regex = new RegExp(`(${safeQuery})`, 'gi');

        if (category.length > 0) {
            html += '<h4>Articles</h4><ul>';
            category.forEach(c => {
                // H
                const highlightedName = c.name.replace(regex, '<mark>$1</mark>');
                html += `<li>${highlightedName}</li>`;

                // html += `<li>${c.name}</li>`; // <li><a href="${c.url}">${c.name}</a></li>
            });
            html += '</ul>';
        }

        if (users.length > 0) {
            html += '<h4>Utilisateurs</h4><ul>';
            users.forEach(user => {
                // H
                const highlightedUsername = user.username.replace(regex, '<mark>$1</mark>');
                html += `<li>${highlightedUsername}</li>`;

                // html += `<li>${user.username}</li>`;
            });
            html += '</ul>';
        }

        if (!category.length && !users.length) {
            html = '<p>Aucun résultat.</p>';
        }
        results.innerHTML = html;
    }
})




// ----- //
/*
import algoliasearch from "algoliasearch";
import { autocomplete } from "@algolia/autocomplete-js";
import "@algolia/autocomplete-theme-classic"; // style par défaut

const client = algoliasearch("FKBH1FY4NZ", "bf2cf1eb0bad313bfb88d4d3e6dbb44a");
const index = client.initIndex("NOM_INDEX");

// Chaque “source” est une section dans l’autocomplete

autocomplete({
  container: "#autocomplete",
  placeholder: "Rechercher...",
  getSources() {
    return [
      {
        sourceId: "produits",
        getItems({ query }) {
          return index.search(query);
        },
        templates: {
          item({ item }) {
            return `
              <div class="aa-ItemWrapper">
                <div class="aa-ItemContent">
                  <strong>${item.name}</strong>
                  <div>${item.category}</div>
                </div>
              </div>
            `;
          },
        },
      },

       { -- Pour avoir plusieurs sources
      sourceId: "articles",
      getItems() {
        return indexArticles.search(query);
      },
      templates: { item({ item }) { return item.title; } }
    }

    ];
  },
});
*/

// -- Placekit -- //
import placekitAutocomplete from "@placekit/autocomplete-js";
import "@placekit/autocomplete-js/dist/placekit-autocomplete.css";

const API_KEY = "pk_kZ8IZ1Ai4nGY4+yqWdIf0uTb+cD4lL6l1zdf/ClICd8=";
const pl = document.querySelector('.pka-input')
if(pl) {
    // Instancie l'autocomplete — target = selector de l'input
    const pka = placekitAutocomplete(API_KEY, {
        target: pl,
        // options utiles :
        maxResults: 6,
        language: "fr",           // "en" ou "fr" (selon docs)
        countries: ["ci"],        // facultatif : limiter aux codes ISO 2 lettres (ici Côte d'Ivoire "ci")
        // format: { value: (item) => `${item.name} ${item.city || ""}` } // si tu veux changer la valeur affichée
    });

    // Quand l'utilisateur choisit une suggestion
    pka.on("pick", (value, item, index) => {
        document.querySelector('#property_form_city').value = item.city
        document.querySelector('#property_form_postal_code').value = item.zipcode
        document.querySelector('#property_form_latitude').value = item.lat
        document.querySelector('#property_form_longitude').value = item.lng



        /*
        // item est l'objet renvoyé par l'API PlaceKit (contient lat, lng, zipcode, city, country, ...)
        document.getElementById("city").value = item.city || "";
        document.getElementById("zipcode").value = (item.zipcode || []).join(", ");
        document.getElementById("country").value = item.country || item.countrycode || "";
        if (item.lat && item.lng) {
        document.getElementById("coords").value = `${item.lat}, ${item.lng}`;
        } else if (item.coordinates) {
        document.getElementById("coords").value = item.coordinates;
        } else {
        document.getElementById("coords").value = "";
        }
        */
    
        // Exemple : si tu veux poster les détails vers ton serveur, fais un fetch ici
        // fetch('/save-address', { method:'POST', body: JSON.stringify(item), headers: {'Content-Type':'application/json'} });
    });
    /*
    // Écoute d'autres événements si besoin
    pka.on("results", (query, results) => {
        // console.log("suggestions", results);
    }).on("error", (err) => {
        console.error("PlaceKit error:", err);
    });
    */
}

const pls = document.querySelector('.search_address')
if(pls) {
    const p = placekitAutocomplete(API_KEY, {
        target: pls,
        // options utiles :
        maxResults: 6,
        language: "fr",           // "en" ou "fr" (selon docs)
        countries: ["ci"],        // facultatif : limiter aux codes ISO 2 lettres (ici Côte d'Ivoire "ci")
        // format: { value: (item) => `${item.name} ${item.city || ""}` } // si tu veux changer la valeur affichée
    });

    // Quand l'utilisateur choisit une suggestion
    p.on("pick", (value, item, index) => {
        document.querySelector('#lat').value = item.lat
        document.querySelector('#lng').value = item.lng
    })
}

/*
    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    const countEl = document.getElementById('notification-count');
    const listEl = document.getElementById('notification-list');

    let dropdownVisible = false;

    bell.addEventListener('click', () => {
        dropdownVisible = !dropdownVisible;
        dropdown.classList.toggle('hidden', !dropdownVisible);
    });

    async function loadNotifications() {
        const response = await fetch('/notifications/latest');
        const data = await response.json();

        // update count
        if (data.unreadCount > 0) {
            countEl.textContent = data.unreadCount;
            countEl.classList.remove('hidden');
        } else {
            countEl.classList.add('hidden');
        }

        // update list
        listEl.innerHTML = '';
        data.notifications.forEach(n => {
            const li = document.createElement('li');
            li.className = p-2 rounded hover:bg-gray-100 ${!n.isRead ? 'bg-gray-50' : ''};
            li.innerHTML = 
                <div class="text-sm font-semibold">${n.title}</div>
                <div class="text-xs text-gray-500">${n.message ?? ''}</div>
                <div class="text-[10px] text-gray-400 mt-1">${n.createdAt}</div>
            ;
            listEl.appendChild(li);
        });
    }

    // poll every 10 seconds
    loadNotifications();
    setInterval(loadNotifications, 10000);

    <a href="">Voir toutes les notifications</a>

    {# <div class="dropdown" id="notification-dropdown">
        <button id="notification-bell" class="btn btn-secondary dropdown-toggle relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown button
            {# <span id="notification-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 hidden">
                dd
            </span>
        </button>
        <ul class="dropdown-menu" id="notification-list">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
    </div> #}


{% block title %}Toutes les notifications{% endblock %}

{% block body %}
<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Toutes les notifications</h2>

        <form action="{{ path('notifications_mark_all_read') }}" method="post">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Tout marquer comme lu
            </button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg divide-y">
        {% for notif in notifications %}
            <div class="p-4 flex justify-between items-start {{ notif.isRead ? '' : 'bg-gray-50' }}">
                <div>
                    <div class="font-semibold text-gray-800">{{ notif.title }}</div>
                    {% if notif.message %}
                        <div class="text-sm text-gray-600">{{ notif.message }}</div>
                    {% endif %}
                    <div class="text-xs text-gray-400 mt-1">
                        {{ notif.createdAt|date('d/m/Y H:i') }}
                    </div>
                </div>
                {% if not notif.isRead %}
                    <form action="{{ path('notifications_mark_single_read', {id: notif.id}) }}" method="post">
                        <button class="text-blue-500 text-sm hover:underline">Marquer comme lu</button>
                    </form>
                {% endif %}
            </div>
        {% else %}
            <div class="p-4 text-gray-500 text-center">Aucune notification trouvée.</div>
        {% endfor %}
    </div>

    {% if totalPages > 1 %}
        <div class="flex justify-center mt-4 space-x-2">
            {% for i in 1..totalPages %}
                <a href="{{ path('notifications_all', {page: i}) }}"
                   class="px-3 py-1 border rounded {{ page == i ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-100' }}">
                   {{ i }}
                </a>
            {% endfor %}
        </div>
    {% endif %}
</div>
{% endblock %}

*/


/*
import { h, render } from 'preact'
import { useState } from 'preact/hooks'

function App() {
    const [count, setCount] = useState(0);

    return (
        <div>
            <h1>Preact</h1>
            <button onClick={() => setCount(count + 1)}>
                Compteur : {count}
            </button>
        </div>
    )
}

render(<App />, document.body)
*/