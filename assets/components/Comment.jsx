import React, { memo, useCallback, useEffect, useRef, useState } from "react"
import { createRoot } from "react-dom/client"
import { useFetch, usePaginateFetch } from "./hooks/hooks"
import { Field } from "./Form"

const dateFormat = {
    dateStyle: 'medium',
    timeStyle: 'short' // Permet de supprimer la partie seconde
}

const VIEW = 'VIEW'
const EDIT = 'EDIT'

function Comments({post, user}) {
    const {items: comments, setItems: setComment, load, loading, count, hasMore} = usePaginateFetch('/fr/api/comments?post=' + post)

    const addComment = useCallback(comment => {
        setComment(prev => [comment, ...prev])
    }, [])

    const deleteComment = useCallback(comment => {
        setComment(prev => prev.filter(c => c !== comment)) // c => c.id !== comment.id) par id
    }, [])

    const updateComment = useCallback((newComment, oldComment) => {
        setComment(prev => prev.map(c => c === oldComment ? newComment : c))
    }, [])

    useEffect(() => {
        load()
    }, []) // Charger les commentaires dès que le composant est monté

    return <div>
        <h3>{count} Commentaire{count > 1 ? 's' : ''}</h3>
        <CommentForm post={post} onComment={addComment} />
        {loading && 'Chargement...'}
        {comments.map(c => (<Comment key={c.id} comment={c} canEdit={c.author && c.author.id === user} onDelete={deleteComment} onUpdate={updateComment} />))}
        {hasMore && <button disabled={loading} className="btn btn-primary" onClick={load}>Charger plus de commentaires</button>}
    </div>
}

const Comment = memo(({comment, onDelete, canEdit, onUpdate}) => { /*
        - Vu que ça ne sert à rien de rerendre le commentaire si elle existe déjà
    */
    const [state, setState] = useState(VIEW)
    const toggleEdit = useCallback(() => {
        setState(state => state === VIEW ? EDIT : VIEW)
    }, [])
    const date = new Date(comment.publishedAt.toString())
    const onDeleteCallback = useCallback(() => {
        onDelete(comment)
    }, [comment])
    const {loading, load} = useFetch(comment['@id'], 'DELETE', onDeleteCallback)

    const onComment = useCallback((newComment) => {
        onUpdate(newComment, comment)
        toggleEdit() // Annule l'édition le formulaire
    }, [comment])

    return <div className="row post-comment">
        <h4 className="col-3">
            <strong>{comment.author && comment.author.username}</strong>
            Commenté le
            <strong>{date.toLocaleString(undefined, dateFormat)}</strong> {/* undefined : Pour qu'il utilise la locale du navigateur */}
        </h4>
        <div className="col-9">
            {state === VIEW ?
                <p>{comment.content}</p> :
                <CommentForm comment={comment} onComment={onComment} onCancel={toggleEdit} />
            }
            {(canEdit && state !== EDIT) && <p>
                <button onClick={load.bind(this, null)} disabled={loading} className="btn btn-danger">Supprimer</button>
                <button onClick={toggleEdit} className="btn btn-primary">Editer</button>
            </p>}
        </div>
    </div>
})

const CommentForm = memo(({post = null, onComment, comment = null, onCancel = null}) => {
    const ref = useRef(null)
    const onSuccess = useCallback(comment => {
        onComment(comment)
        ref.current.value = ''
    }, [ref, onComment])

    const method = comment ? 'PATCH' : 'POST'
    const url = comment ? comment['@id'] : '/fr/api/comments'
    const {loading, errors, load, clearErrors} = useFetch(url, method, onSuccess)
    const onSubmit = useCallback(e => {
        e.preventDefault()
        load({
            content: ref.current.value,
            property: '/fr/api/posts/' + post
        })
    }, [load, ref, post]) // 'load' ne devrait changer

    useEffect(() => {
        if(comment && comment.content && ref.current) {
            ref.current.value = comment.content
        }
    }, [comment, ref]) // 'ref' ne devrait changer

    return <div>
        <form onSubmit={onSubmit}>
            {comment === null && <fieldset><legend>Laisser un commentaire</legend></fieldset>}
            <Field
                name="content"
                ref={ref}
                required
                minLength={4}
                help="Les commentaires non conformes à notre code de conduite seront modérés"
                error={errors['content']}
                onChange={clearErrors.bind(this, 'content')}
            >
                Votre commentaire
            </Field>
            <button disabled={loading} className="btn btn-primary">
                {comment === null ? 'Envoyer' : 'Editer'}
            </button>
            {onCancel && <button className="btn btn-secondary" onClick={onCancel}>
                Annuler
            </button>}
        </form>
    </div>
})

class CommentsElement extends HTMLElement {

    constructor() {
        super()
        this.observer = null
    }

    connectedCallback() {
        const post = parseInt(this.dataset.post, 10)
        const user = parseInt(this.dataset.user, 10) || null
        if(this.observer === null) {
            this.observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting && entry.target === this) {
                        observer.disconnect()
                        createRoot(this).render(<Comments post={post} user={user} />)
                    }
                })
            })
        }
        this.observer.observe(this)
    }

    deconnectedCallback() { /*
            - On peut prévoir même si ça n'arrivera jamais
        */
        if(this.observer) {
            this.observer.disconnect()
        }
        createRoot(this).unmount()
    }
}

customElements.define('posts-comments', CommentsElement)