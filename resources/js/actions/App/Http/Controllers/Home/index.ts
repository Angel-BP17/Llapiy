import AuthController from './AuthController'
import DashboardController from './DashboardController'
import SystemController from './SystemController'
const Home = {
    AuthController: Object.assign(AuthController, AuthController),
DashboardController: Object.assign(DashboardController, DashboardController),
SystemController: Object.assign(SystemController, SystemController),
}

export default Home