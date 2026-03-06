import Link from "next/link"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { 
  CalendarCheck, 
  Clock, 
  Users, 
  TrendingUp,
  ArrowRight,
  CheckCircle,
  XCircle,
} from "lucide-react"

// Mock data
const stats = [
  { label: "Today's Reservations", value: "24", icon: CalendarCheck, trend: "+3 from yesterday" },
  { label: "Pending Approval", value: "5", icon: Clock, trend: "Requires attention" },
  { label: "Total Guests Today", value: "86", icon: Users, trend: "+12% vs avg" },
  { label: "Occupancy Rate", value: "78%", icon: TrendingUp, trend: "Peak hours: 7-9 PM" },
]

const recentReservations = [
  {
    id: "RES-2026-0320",
    guest: "Sarah Johnson",
    partySize: 4,
    zone: "Main Dining Room",
    time: "7:30 PM",
    status: "pending",
  },
  {
    id: "RES-2026-0319",
    guest: "Michael Brown",
    partySize: 2,
    zone: "The Bar",
    time: "6:00 PM",
    status: "confirmed",
  },
  {
    id: "RES-2026-0318",
    guest: "Emily Davis",
    partySize: 6,
    zone: "The Patio",
    time: "8:00 PM",
    status: "pending",
  },
  {
    id: "RES-2026-0317",
    guest: "James Wilson",
    partySize: 2,
    zone: "Main Dining Room",
    time: "7:00 PM",
    status: "confirmed",
  },
]

const zoneStatus = [
  { name: "Main Dining Room", tables: 20, occupied: 14, available: 6 },
  { name: "The Patio", tables: 10, occupied: 6, available: 4 },
  { name: "The Bar", tables: 6, occupied: 5, available: 1 },
]

export default function AdminDashboardPage() {
  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">Dashboard</h1>
          <p className="text-muted-foreground mt-1">
            Welcome back, Marcus. Here{"'"}s what{"'"}s happening today.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <span className="text-sm text-muted-foreground">
            {new Date().toLocaleDateString("en-US", { 
              weekday: "long", 
              year: "numeric", 
              month: "long", 
              day: "numeric" 
            })}
          </span>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((stat) => (
          <Card key={stat.label}>
            <CardContent className="p-6">
              <div className="flex items-start justify-between">
                <div className="space-y-1">
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                  <p className="text-3xl font-semibold text-foreground">{stat.value}</p>
                  <p className="text-xs text-muted-foreground">{stat.trend}</p>
                </div>
                <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                  <stat.icon className="h-5 w-5 text-primary" />
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Recent Reservations */}
        <div className="lg:col-span-2">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle className="font-serif text-lg">Recent Reservations</CardTitle>
              <Link href="/admin/reservations">
                <Button variant="ghost" size="sm" className="text-primary">
                  View All
                  <ArrowRight className="h-4 w-4 ml-1" />
                </Button>
              </Link>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentReservations.map((reservation) => (
                  <div
                    key={reservation.id}
                    className="flex items-center justify-between p-4 bg-muted rounded-sm"
                  >
                    <div className="space-y-1">
                      <div className="flex items-center gap-2">
                        <span className="font-medium text-foreground">{reservation.guest}</span>
                        <Badge
                          variant={reservation.status === "confirmed" ? "default" : "secondary"}
                          className={
                            reservation.status === "confirmed"
                              ? "bg-green-100 text-green-700 hover:bg-green-100"
                              : "bg-yellow-100 text-yellow-700 hover:bg-yellow-100"
                          }
                        >
                          {reservation.status}
                        </Badge>
                      </div>
                      <p className="text-sm text-muted-foreground">
                        {reservation.zone} • {reservation.partySize} guests • {reservation.time}
                      </p>
                    </div>
                    {reservation.status === "pending" && (
                      <div className="flex gap-2">
                        <Button size="sm" variant="outline" className="text-green-600 hover:text-green-600">
                          <CheckCircle className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="outline" className="text-destructive hover:text-destructive">
                          <XCircle className="h-4 w-4" />
                        </Button>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Zone Status */}
        <div>
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg">Zone Status</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              {zoneStatus.map((zone) => (
                <div key={zone.name} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium text-foreground">{zone.name}</span>
                    <span className="text-sm text-muted-foreground">
                      {zone.occupied}/{zone.tables} tables
                    </span>
                  </div>
                  <div className="h-2 bg-muted rounded-full overflow-hidden">
                    <div
                      className="h-full bg-primary rounded-full transition-all"
                      style={{ width: `${(zone.occupied / zone.tables) * 100}%` }}
                    />
                  </div>
                  <p className="text-xs text-muted-foreground">
                    {zone.available} tables available
                  </p>
                </div>
              ))}
              
              <Link href="/admin/floor" className="block pt-2">
                <Button variant="outline" size="sm" className="w-full">
                  Manage Floor Plan
                  <ArrowRight className="h-4 w-4 ml-2" />
                </Button>
              </Link>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Quick Actions */}
      <Card>
        <CardHeader>
          <CardTitle className="font-serif text-lg">Quick Actions</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <Link href="/admin/reservations?status=pending">
              <Button variant="outline" className="w-full h-auto py-4 flex flex-col gap-2">
                <Clock className="h-5 w-5 text-primary" />
                <span className="text-sm">Pending Approvals</span>
              </Button>
            </Link>
            <Link href="/admin/floor">
              <Button variant="outline" className="w-full h-auto py-4 flex flex-col gap-2">
                <CalendarCheck className="h-5 w-5 text-primary" />
                <span className="text-sm">Table Assignment</span>
              </Button>
            </Link>
            <Link href="/admin/users">
              <Button variant="outline" className="w-full h-auto py-4 flex flex-col gap-2">
                <Users className="h-5 w-5 text-primary" />
                <span className="text-sm">Guest Directory</span>
              </Button>
            </Link>
            <Link href="/admin/reports">
              <Button variant="outline" className="w-full h-auto py-4 flex flex-col gap-2">
                <TrendingUp className="h-5 w-5 text-primary" />
                <span className="text-sm">View Reports</span>
              </Button>
            </Link>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
