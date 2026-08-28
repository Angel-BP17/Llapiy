import Home from './Home'
import Users from './Users'
import Documents from './Documents'
import Storage from './Storage'
import Inbox from './Inbox'
import DocumentTypes from './DocumentTypes'
import Areas from './Areas'
import Activities from './Activities'
import Configuration from './Configuration'
const Controllers = {
    Home: Object.assign(Home, Home),
Users: Object.assign(Users, Users),
Documents: Object.assign(Documents, Documents),
Storage: Object.assign(Storage, Storage),
Inbox: Object.assign(Inbox, Inbox),
DocumentTypes: Object.assign(DocumentTypes, DocumentTypes),
Areas: Object.assign(Areas, Areas),
Activities: Object.assign(Activities, Activities),
Configuration: Object.assign(Configuration, Configuration),
}

export default Controllers