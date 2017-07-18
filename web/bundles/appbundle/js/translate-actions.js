function translateActions(action) {
    actionText = "";
    switch (action) {
        case "new":
            actionText = "Moneda creada por ";
            break;
        case  "pending" :
            actionText = "Pendiente recogida de moneda por ";
            break;
        case  "transfer" :
            actionText = "Moneda transferida a ";
            break;
        case  "grab" :
            actionText = "Moneda recogida por ";
            break;
        case  "anonymousgrab" :
            actionText = "Moneda recogida anónimamente";
            break;
        case  "release" :
            actionText = "Moneda liberada por ";
            break;
        case  "publickey" :
            actionText = "Moneda protegida con llave publica por ";
            break;
        case  "claim" :
            actionText = "Moneda reclamada por ";
            break;
        case  "review" :
            actionText = "Moneda utilizada por ";
            break;

    }
    return actionText;
}