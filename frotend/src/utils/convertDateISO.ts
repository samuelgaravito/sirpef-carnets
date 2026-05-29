function convertDateISO(fechaIso: string) {
    const fecha = new Date(fechaIso);
    
    const day = String(fecha.getDate()).padStart(2, '0');
    const month = String(fecha.getMonth() + 1).padStart(2, '0');
    const year = fecha.getFullYear();

    return `${day}/${month}/${year}`;
}

export default convertDateISO