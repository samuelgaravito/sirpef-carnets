function convertDateISOToHour(fechaIso: string) {
    const fecha = new Date(fechaIso);
    
    const hours = String(fecha.getHours()).padStart(2, '0');
    const minutes = String(fecha.getMinutes()).padStart(2, '0');
    const seconds = String(fecha.getSeconds()).padStart(2, '0');
    
    return `${hours}:${minutes}:${seconds}`;
}

export default convertDateISOToHour