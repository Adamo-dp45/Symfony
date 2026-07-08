import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    /**
     * Lorsque l'élément apparaît dans le DOM
     */
    connect() {
        this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
    }

    /**
     * Lorsque l'élément disparaît dans le DOM
     */
    disconnect() {

    }

    /**
     * Lors de l'instanciation du controleur
     */
    initialize() {

    }
}
