import UserService from "#features/users/user.service.js";
import { bcryptUtil } from "#utils/index.js";
import AuthMessages from "./auth.message.js";

const AuthService = {
    registerUser: async (payload) => {
        await UserService.checkExistedUser(payload);

        const hashedPassword = await bcryptUtil.hashPassword(payload.password);

        const { confirmedPassword, ...userData } = payload;

        const newUser = await UserService.insertUser({ ...userData, password: hashedPassword });

        return newUser;
    },
};

export default AuthService;
