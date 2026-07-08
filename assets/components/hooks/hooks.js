import { useCallback, useState } from "react"

async function jsonLdFetch(url, method = 'GET', data = null) {
    const params = {
        method: method,
        headers: {
            "Accept": "application/ld+json",
            "Content-Type": method === 'PATCH' ? "application/merge-patch+json" : "application/ld+json"
        }
    }
    if(data) {
        params.body = JSON.stringify(data)
    }
    const r = await fetch(url, params)
    if(r.status === 204) {
        return null
    }
    const responseData = await r.json()
    if(r.ok) {
        return responseData
    } else {
        throw responseData
    }
}

export function usePaginateFetch(url) {
    const [loading, setLoading] = useState(false)
    const [items, setItems] = useState([])
    const [count, setCount] = useState(0)
    const [next, setNext] = useState(null)
    const [error, setError] = useState(null)

    const load = useCallback(async () => {
        setLoading(true)
        try {
            const r = await jsonLdFetch(next || url)
            setItems(items => [...items, ...r['member']]) // Les items déjà disponible et rajouter les items qui sont charger
            setCount(r['totalItems'])
            if(r['view'] && r['view']['next']) {
                setNext(r['view']['next'])
            } else {
                setNext(null)
            }
        } catch(e) {
            console.log(e)
            setError('Une erreur s\'est produite lors du chargement des avis')
        }
        /*
            const r = await fetch(next || url, { -- Pour recharger la page suivante ou l'url
                headers: {
                    "Accept": "application/ld+json"
                }
            })
            const data = await r.json()
            if(r.ok) {
                setItems(items => [...items, ...data['member']]) -- Les 'items' déjà disponible et rajouter les 'items' qui sont charger
                setCount(data['totalItems'])
                if(data['view'] && data['view']['next']) { -- Vu que 'next' n'est pas renvoyer lorsqu'on n'est sur la dernière page
                    setNext(data['view']['next'])
                } else {
                    setNext(null)
                }
            } else {
                console.error(data)
            }
        */
        setLoading(false)
    }, [url, next])

    return {
        items,
        setItems,
        load,
        loading,
        count,
        hasMore: next !== null, // On lui renvoi ça seulement car on n'a pas besoin de connaître l'url de la page suivante
        error
    }
}

export function useFetch(url, method = 'POST', callback = null) {
    const [errors, setErrors] = useState({})
    const [loading, setLoading] = useState(false)

    const load = useCallback(async (data = null) => {
        setLoading(true)
        try {
            const r = await jsonLdFetch(url, method, data)
            setLoading(false)
            if(callback) {
                callback(r)
            }
        } catch(e) {
            setLoading(false)
            if(e.violations) {
                setErrors(e.violations.reduce((acc, violation) => { /*
                        - Va retourner la reponse de l'api et j'utilise la reponse de validation
                    */
                    acc[violation.propertyPath] = violation.message
                    return acc
                }, {}))
            } else {
                throw e
            }
        }
    }, [url, method, callback]) // Va changer la fonction à chaque fois qu'une des dépendances change

    const clearErrors = useCallback((name) => {
        if(errors[name]) {
            setErrors(errors => ({...errors, [name]: null}))
        }
    }, [errors])

    return {
        loading,
        errors,
        load,
        clearErrors
    }
}