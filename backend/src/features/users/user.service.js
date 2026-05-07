import { UserModel } from "#models/index.js";
import UserMessages from "./user.message.js";

const UserService = {
    checkExistedUser: async (payload) => {
        const existedUser = await UserModel.findOne({
            $or: [{ username: payload.username }, { email: payload.email }],
        });

        if (!existedUser) {
            return null;
        }

        if (existedUser.username === payload.username) {
            throw UserMessages.error.USERNAME_IS_EXIST();
        }

        if (existedUser.email === payload.email) {
            throw UserMessages.error.EMAIL_IS_EXIST();
        }

        return existedUser;
    },

    insertUser: async (payload) => {
        return await UserModel.create(payload);
    },
};

export default UserService;
