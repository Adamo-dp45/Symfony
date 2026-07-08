import { useState } from "react"

function App() {
    const [count, setCount] = useState(0)
    const increment = () => {
        setCount((count) => count + 1)
    }

    return <>
        <div className="lead">
            React
            {/*
                {count}
                <button className="btn btn-primary" onClick={increment}>Incrémenter</button>
            */}
        </div>
    </>
}

export default App