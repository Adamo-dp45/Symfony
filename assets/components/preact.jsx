import { h, render } from 'preact'
import { useState } from 'preact/hooks'

function App() {
    return (
        <p>
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Distinctio cupiditate aliquid officia corporis quo, consequatur, quam accusantium, voluptate ex culpa ut repellat nulla non et dignissimos rem iure quas soluta.
        </p>
    )
}

const mountNode = document.getElementById('root')
if(mountNode) {
    render(<App />, mountNode)
}