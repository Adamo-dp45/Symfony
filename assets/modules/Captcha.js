function clamp(n, min, max) {
    return Math.min(Math.max(n, min), max)
}

/**
 * Un nombre aléatoire
 */
function randomNumberBetween(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min)
}

class PuzzleCaptcha extends HTMLElement {

    connectedCallback() {
        const width = parseInt(this.getAttribute('width'), 10)
        const height = parseInt(this.getAttribute('height'), 10)
        const pieceWidth = parseInt(this.getAttribute('piece-width'), 10)
        const pieceHeight = parseInt(this.getAttribute('piece-height'), 10)
        this.classList.add('captcha')
        this.classList.add('captcha-waiting-interaction') // On n'a pas encore cliqué la dessus
        this.style.setProperty('--width', `${width}px`)
        this.style.setProperty('--height', `${height}px`)
        this.style.setProperty('--piece-width', `${pieceWidth}px`)
        this.style.setProperty('--piece-height', `${pieceHeight}px`)
        this.style.setProperty('--image', `url(${this.getAttribute('src')})`)

        const maxX = width - pieceWidth
        const maxY = height - pieceHeight

        const input = this.querySelector('.captcha-answer')
        const piece = document.createElement('div')
        piece.classList.add('captcha-piece')
        this.appendChild(piece)

        let isDragging = false
        // let position = {x: 0, y: 0} -- On veut avoir une position aléatoire dès le départ
        let position = {x: randomNumberBetween(0, maxX), y: randomNumberBetween(0, maxY)}
        piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`)

        piece.addEventListener('pointerdown', e => {
            isDragging = true
            document.body.style.setProperty('user-select', 'none') // Pour désactiver la selection de texte au moment du drag
            this.classList.remove('captcha-waiting-interaction')
            piece.classList.add('is-moving')

            window.addEventListener( /*
                    - Pour éviter de sortir dans la zone avec la piece et qu'on relache la souris
                */
                'pointerup',
                () => {
                    document.body.style.removeProperty('user-select') // Si on laisse la souris on peut selectionner du texte
                    piece.classList.remove('is-moving')
                    isDragging = false
                }, {once: true}
            )
        })

        this.addEventListener('pointermove', e => {
            if(!isDragging) {
                return
            }
            // position.x += e.movementX
            position.x = clamp(position.x + e.movementX, 0, maxX)
            position.y = clamp(position.y + e.movementY, 0, maxY)
            piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`)
            input.value = `${position.x}-${position.y}`
        })
    }

}

customElements.define('puzzle-captcha', PuzzleCaptcha)