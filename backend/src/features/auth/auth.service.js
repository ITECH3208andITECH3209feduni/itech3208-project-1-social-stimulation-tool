import UserService from "#features/users/user.service.js";
import { bcryptUtil, jwtUtil } from "#utils/index.js";
import AuthMessages from "./auth.message.js";

const AuthService = {
    registerUser: async (payload) => {
        await UserService.checkExistedUser(payload);

        const hashedPassword = await bcryptUtil.hashPassword(payload.password);

        const { confirmedPassword, ...userData } = payload;

        const newUser = await UserService.insertUser({ ...userData, password: hashedPassword });

        return newUser;
    },

    loginUser: async (input) => {
        const validUser = await UserService.checkCredential(input);
        
        const payload = {
            id: validUser._id,
            avatar: validUser.avatar,
            firstName: validUser.firstName,
            lastName: validUser.lastName,
            role: validUser.role,
        };

        const accessToken = jwtUtil.genAccessToken(payload);

        return {
            accessToken,
        };
    },
};

export default AuthService;
