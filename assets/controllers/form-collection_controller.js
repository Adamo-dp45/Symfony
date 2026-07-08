import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    //  static values = { // Spécifique au controller de symfony
    //    addLabel: string,
    //    deleteLabel: string // La valeur sera données dans le FormType
    //  }

    connect() {
        this.index = this.element.childElementCount // L'index est par défaut sur l'élément avec __name et on le remplace par le nombre d'élément
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.innerText = /*this.addLabelValue ||*/ 'Ajouter un élément'
        btn.setAttribute('type', 'button')
        btn.addEventListener('click', this.addElement)
        this.element.childNodes.forEach(this.addDeleteButton)
        this.element.append(btn)
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    addElement = (e) => {
        e.preventDefault()
        const element = document.createRange().createContextualFragment( // createRange permet de créer un élément html à partir d'une chaîne de caratère
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild // Car je sais qu'il n'y a qu'un élément racine, ça me permet d'avoir un élément et pas un fragment
        this.addDeleteButton(element)
        this.index++
        e.currentTarget.insertAdjacentElement('beforebegin', element)
    }

    /**
     * 
     * @param {HTMLElement} item 
     */
    addDeleteButton = (item) => {
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-danger')
        btn.innerText = this.deleteLabelValue || 'Supprimer'
        btn.setAttribute('type', 'button')
        item.append(btn)
        btn.addEventListener('click', e => {
            e.preventDefault()
            item.remove()
        })
    }
}
