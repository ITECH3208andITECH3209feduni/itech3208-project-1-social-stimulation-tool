import { Outlet } from "react-router-dom"
import Navbar from "@/layouts/Navbar"

function HomeLayout() {
  return (
    <div>
      <Navbar />
      <Outlet />
    </div>
  )
}

export default HomeLayout  