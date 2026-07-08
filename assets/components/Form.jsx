export function Field({ref, help, name, error, children, onChange, required, minLength}) {
    const className = (...arr) => arr.filter(Boolean).join(' ') /*
        - On filtre les valeurs qui vont être null et qu'il rejoint par caractère et un espace
    */
    if(error) {
        help = error
    }

    return <div className={className("form-group", error && "has-error")}>
        <label htmlFor={name}>{children}</label>
        <textarea ref={ref} name={name} id={name} className="form-control" onChange={onChange} required={required} minLength={minLength}></textarea>
        {help && <div>{help}</div>}
    </div>
}