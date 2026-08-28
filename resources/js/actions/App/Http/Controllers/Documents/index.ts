import DocumentController from './DocumentController'
import BlockController from './BlockController'
import DocumentarySeriesController from './DocumentarySeriesController'
const Documents = {
    DocumentController: Object.assign(DocumentController, DocumentController),
BlockController: Object.assign(BlockController, BlockController),
DocumentarySeriesController: Object.assign(DocumentarySeriesController, DocumentarySeriesController),
}

export default Documents