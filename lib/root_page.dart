import 'package:flutter/material.dart';
import 'package:my_app/pages/profile.dart';
import 'package:my_app/pages/home.dart';

class RootPage extends StatefulWidget {
  const RootPage({super.key});

  @override
  State<RootPage> createState() => _HomeState();
}

class _HomeState extends State<RootPage> {
  int currentPage = 0;
  List<Widget> pages = const [
    Home(),
    ProfilePage(),
  ];
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Sandwiches'),
      ),
      body: pages[currentPage],
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          debugPrint('Floating Action Button clicked.');
        },
        child: const Icon(Icons.add),
      ),
      bottomNavigationBar: NavigationBarTheme(
        data: NavigationBarThemeData(
          indicatorColor: Colors.yellow.shade700,
        ),
        child: NavigationBar(
          //backgroundColor: Colors.amber,
          destinations: const [
            NavigationDestination(
                icon: Icon(
                  Icons.home,
                ),
                label: 'RootPage'),
            NavigationDestination(
                icon: Icon(
                  Icons.person,
                ),
                label: 'Profile'),
          ],
          onDestinationSelected: (int index) {
            setState(() {
              currentPage = index;
              debugPrint("Questa è la pagina: $index");
            });
          },
          selectedIndex: currentPage,
        ),
      ),
    );
  }
}
