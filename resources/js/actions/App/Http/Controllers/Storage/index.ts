import SectionController from './SectionController'
import AndamioController from './AndamioController'
import BoxController from './BoxController'
import ArchivoController from './ArchivoController'
const Storage = {
    SectionController: Object.assign(SectionController, SectionController),
AndamioController: Object.assign(AndamioController, AndamioController),
BoxController: Object.assign(BoxController, BoxController),
ArchivoController: Object.assign(ArchivoController, ArchivoController),
}

export default Storage