import ProfileController from './ProfileController'
import UserController from './UserController'
import RoleController from './RoleController'
const Users = {
    ProfileController: Object.assign(ProfileController, ProfileController),
UserController: Object.assign(UserController, UserController),
RoleController: Object.assign(RoleController, RoleController),
}

export default Users