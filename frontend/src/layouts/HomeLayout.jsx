import { Outlet } from "react-router-dom";
import Navbar from "@/components/common/Navbar";
function HomeLayout() {
    return (
        <div>
            <Navbar />
            <Outlet />
        </div>
    );
}

export default HomeLayout;
